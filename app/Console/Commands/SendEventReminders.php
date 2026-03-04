<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Instrument;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders
                            {--dry-run : Simula el envío sin enviar mensajes reales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de WhatsApp para eventos del día siguiente';

    /**
     * Servicio de WhatsApp
     */
    protected WhatsAppService $whatsappService;

    /**
     * Contadores para el resumen
     */
    protected int $sentCount = 0;
    protected int $failedCount = 0;
    protected int $skippedCount = 0;

    public function __construct()
    {
        parent::__construct();
        $this->whatsappService = new WhatsAppService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('========================================');
        $this->info('  Sistema de Recordatorios WhatsApp');
        $this->info('========================================');
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO SIMULACIÓN (dry-run): No se enviarán mensajes reales');
            $this->newLine();
        }

        // Obtener eventos del día siguiente
        $tomorrow = now()->addDay()->startOfDay();
        $dayAfterTomorrow = now()->addDay()->endOfDay();

        $this->info("Buscando eventos para: {$tomorrow->format('d/m/Y')}");
        $this->newLine();

        $events = Event::whereBetween('date', [$tomorrow, $dayAfterTomorrow])
            ->with(['users' => function ($query) {
                $query->wherePivot('status', '!=', 'declined');
            }, 'users.instruments'])
            ->get();

        if ($events->isEmpty()) {
            $this->warn('No hay eventos programados para mañana.');
            return Command::SUCCESS;
        }

        $this->info("Se encontraron {$events->count()} evento(s) para mañana.");
        $this->newLine();

        foreach ($events as $event) {
            $this->processEvent($event, $isDryRun);
        }

        // Mostrar resumen
        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Procesa un evento y envía recordatorios a los miembros asignados
     */
    protected function processEvent(Event $event, bool $isDryRun): void
    {
        $this->info("📅 Evento: {$event->name}");
        $this->info("   Fecha: {$event->date->format('d/m/Y h:i A')}");
        $this->newLine();

        $assignedUsers = $event->users;

        if ($assignedUsers->isEmpty()) {
            $this->warn('   No hay miembros asignados a este evento.');
            $this->newLine();
            return;
        }

        $bar = $this->output->createProgressBar($assignedUsers->count());
        $bar->start();

        foreach ($assignedUsers as $user) {
            $this->processUser($user, $event, $isDryRun);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    /**
     * Procesa un usuario y envía el recordatorio
     */
    protected function processUser($user, Event $event, bool $isDryRun): void
    {
        // Verificar que tenga teléfono
        if (empty($user->phone)) {
            $this->verboseLog("   ⚠️  {$user->name}: Sin número de teléfono");
            $this->skippedCount++;
            return;
        }

        // Obtener el instrumento asignado
        $instrumentId = $user->pivot->instrument_id ?? null;
        $instrument = $instrumentId ? Instrument::find($instrumentId) : null;

        if (!$instrument) {
            $this->verboseLog("   ⚠️  {$user->name}: Sin instrumento asignado");
            $this->skippedCount++;
            return;
        }

        // En modo dry-run, solo simular
        if ($isDryRun) {
            $this->verboseLog("   📤 {$user->name}: Se enviaría recordatorio para {$instrument->name}");
            $this->sentCount++;
            return;
        }

        // Enviar recordatorio real
        try {
            $result = $this->whatsappService->sendEventReminder($user, $event, $instrument);

            if ($result['success']) {
                $this->verboseLog("   ✅ {$user->name}: Recordatorio enviado");
                $this->sentCount++;
                
                Log::info("Recordatorio enviado a {$user->name} ({$user->phone}) para evento {$event->name}");
            } else {
                $this->verboseLog("   ❌ {$user->name}: {$result['message']}");
                $this->failedCount++;
                
                Log::error("Error enviando recordatorio a {$user->name}: {$result['message']}");
            }
        } catch (\Exception $e) {
            $this->verboseLog("   ❌ {$user->name}: Error - {$e->getMessage()}");
            $this->failedCount++;
            
            Log::error("Excepción enviando recordatorio a {$user->name}: " . $e->getMessage());
        }
    }

    /**
     * Muestra el resumen final
     */
    protected function displaySummary(): void
    {
        $this->info('========================================');
        $this->info('  RESUMEN DE ENVÍO');
        $this->info('========================================');
        $this->info("✅ Enviados:     {$this->sentCount}");
        $this->info("❌ Fallidos:     {$this->failedCount}");
        $this->info("⏭️  Omitidos:    {$this->skippedCount}");
        $this->info('========================================');
    }

    /**
     * Muestra mensaje verbose si está habilitado
     */
    protected function verboseLog(string $message): void
    {
        if ($this->option('verbose') || $this->option('dry-run')) {
            $this->line($message);
        }
    }
}
