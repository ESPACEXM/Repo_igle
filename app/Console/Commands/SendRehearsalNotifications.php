<?php

namespace App\Console\Commands;

use App\Models\Rehearsal;
use App\Models\User;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRehearsalNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-rehearsals 
                            {--days=1 : Días de anticipación para enviar recordatorios}
                            {--dry-run : Simular el envío sin enviar mensajes reales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía notificaciones de Telegram a los usuarios sobre ensayos próximos';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram): int
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        $this->info("🎵 Enviando notificaciones de ensayos");
        $this->line("   Días de anticipación: {$days}");
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

        $this->info("📅 Buscando ensayos entre {$targetDate->format('d/m/Y')} y {$endOfTargetDate->format('d/m/Y')}");
        $this->line('');

        // Buscar ensayos en el rango de fechas
        $rehearsals = Rehearsal::whereBetween('date', [$targetDate, $endOfTargetDate])
            ->with(['event', 'event.users', 'event.users.instruments'])
            ->get();

        if ($rehearsals->isEmpty()) {
            $this->warn('⚠️ No se encontraron ensayos para notificar.');
            return 0;
        }

        $this->info("🎵 Se encontraron {$rehearsals->count()} ensayo(s)");
        $this->line('');

        $totalSent = 0;
        $totalFailed = 0;
        $totalSkipped = 0;

        foreach ($rehearsals as $rehearsal) {
            $event = $rehearsal->event;
            
            if (!$event) {
                $this->warn("⚠️ Ensayo sin evento asociado (ID: {$rehearsal->id})");
                continue;
            }

            $this->info("🎵 Ensayo para: {$event->name}");
            $this->line("   📅 {$rehearsal->date->format('d/m/Y h:i A')}");
            $this->line("   📍 " . ($rehearsal->location ?: 'Ubicación por confirmar'));
            $this->line('');

            // Obtener usuarios asignados al evento
            foreach ($event->users as $user) {
                // Verificar si el usuario tiene Telegram configurado
                if (empty($user->telegram_chat_id)) {
                    $this->warn("   ⚠️ {$user->name}: No tiene Telegram configurado");
                    $totalSkipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->info("   📤 [SIMULACIÓN] Enviar a {$user->name} ({$user->telegram_chat_id})");
                    $totalSent++;
                    continue;
                }

                // Enviar mensaje
                try {
                    $result = $telegram->sendRehearsalConfirmationMessage(
                        $user->telegram_chat_id,
                        $user,
                        $rehearsal,
                        $event
                    );

                    if ($result['success']) {
                        $this->info("   ✅ {$user->name}: Mensaje enviado");
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
