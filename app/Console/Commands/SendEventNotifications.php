<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-events 
                            {--days=1 : Días de anticipación para enviar recordatorios}
                            {--type=confirmation : Tipo de notificación: confirmation (requiere respuesta) o reminder (informativo)}
                            {--dry-run : Simular el envío sin enviar mensajes reales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones de Telegram a los usuarios asignados a eventos próximos';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram): int
    {
        $days = (int) $this->option('days');
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        $this->info("🔔 Enviando notificaciones de eventos");
        $this->line("   Días de anticipación: {$days}");
        $this->line("   Tipo: {$type}");
        $this->line("   Modo: " . ($dryRun ? 'Simulación' : 'Producción'));
        $this->line('');

        // Verificar configuración de Telegram
        if (!$telegram->isConfigured()) {
            $this->error('❌ Telegram no está configurado. Verifica TELEGRAM_BOT_TOKEN en .env');
            return 1;
        }

        // Calcular fecha objetivo
        $targetDate = Carbon::now()->addDays($days)->startOfDay();
        $endOfTargetDate = Carbon::now()->addDays($days)->endOfDay();

        $this->info("📅 Buscando eventos entre {$targetDate->format('d/m/Y')} y {$endOfTargetDate->format('d/m/Y')}");
        $this->line('');

        // Buscar eventos en el rango de fechas
        $events = Event::whereBetween('date', [$targetDate, $endOfTargetDate])
            ->with(['users' => function ($query) {
                $query->wherePivot('status', 'pending')
                    ->orWhereNull('event_user.status');
            }, 'users.instruments'])
            ->get();

        if ($events->isEmpty()) {
            $this->warn('⚠️ No se encontraron eventos para notificar.');
            return 0;
        }

        $this->info("📊 Se encontraron {$events->count()} evento(s)");
        $this->line('');

        $totalSent = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach ($events as $event) {
            $this->info("📅 Evento: {$event->name} ({$event->date->format('d/m/Y h:i A')})");

            foreach ($event->users as $user) {
                // Verificar si el usuario tiene Telegram configurado
                if (empty($user->telegram_chat_id)) {
                    $this->warn("   ⚠️ {$user->name}: No tiene Telegram configurado");
                    $totalSkipped++;
                    continue;
                }

                // Verificar si ya se envió la notificación
                $pivotData = $user->pivot;
                if ($pivotData->notification_sent && !$dryRun) {
                    $this->line("   ℹ️ {$user->name}: Notificación ya enviada anteriormente");
                    $totalSkipped++;
                    continue;
                }

                // Obtener instrumento asignado
                $instrument = $user->instruments
                    ->where('id', $pivotData->instrument_id)
                    ->first();

                if (!$instrument) {
                    $this->warn("   ⚠️ {$user->name}: No tiene instrumento asignado");
                    $totalSkipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->info("   📤 [SIMULACIÓN] Enviar a {$user->name} ({$user->telegram_chat_id})");
                    $this->line("      Instrumento: {$instrument->name}");
                    $this->line("      Tipo: " . ($type === 'confirmation' ? 'Confirmación requerida' : 'Recordatorio informativo'));
                    $totalSent++;
                    continue;
                }

                // Enviar mensaje según el tipo
                try {
                    $requiresConfirmation = $type === 'confirmation';
                    
                    $result = $telegram->sendEventConfirmationMessage(
                        $user->telegram_chat_id,
                        $user,
                        $event,
                        $instrument,
                        $requiresConfirmation
                    );

                    if ($result['success']) {
                        $this->info("   ✅ {$user->name}: Mensaje enviado");
                        
                        // Actualizar registro en base de datos
                        $event->users()->updateExistingPivot($user->id, [
                            'notification_sent' => true,
                            'requires_confirmation' => $requiresConfirmation,
                            'notification_type' => $type,
                        ]);
                        
                        $totalSent++;
                    } else {
                        $this->error("   ❌ {$user->name}: {$result['message']}");
                        $totalFailed++;
                    }
                } catch (\Exception $e) {
                    $this->error("   ❌ {$user->name}: Error - {$e->getMessage()}");
                    $totalFailed++;
                }
            }

            $this->line('');
        }

        // Resumen
        $this->info('📊 Resumen:');
        $this->line("   ✅ Enviados: {$totalSent}");
        $this->line("   ❌ Fallidos: {$totalFailed}");
        $this->line("   ⏭️ Omitidos: {$totalSkipped}");

        return $totalFailed > 0 ? 1 : 0;
    }
}
