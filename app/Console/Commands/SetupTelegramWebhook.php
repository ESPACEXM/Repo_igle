<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SetupTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:setup-webhook {url? : URL pública para el webhook (opcional, usa ngrok por defecto)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura el webhook de Telegram usando ngrok o una URL personalizada';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Configurando webhook de Telegram...\n');

        // Obtener el token del bot
        $botToken = config('services.telegram.bot_token');
        if (empty($botToken)) {
            $this->error('❌ TELEGRAM_BOT_TOKEN no está configurado en el archivo .env');
            return 1;
        }

        // Obtener la URL pública
        $publicUrl = $this->argument('url');

        if (empty($publicUrl)) {
            // Intentar obtener la URL de ngrok
            $this->info('🔍 Buscando URL de ngrok...');
            $publicUrl = $this->getNgrokUrl();

            if (empty($publicUrl)) {
                $this->error('❌ No se encontró una URL de ngrok activa.');
                $this->line('');
                $this->line('Por favor:');
                $this->line('  1. Inicia ngrok en otra terminal:');
                $this->line('     ngrok http 8000');
                $this->line('');
                $this->line('  2. O proporciona una URL manualmente:');
                $this->line('     php artisan telegram:setup-webhook https://tu-url.com');
                return 1;
            }
        }

        // Construir la URL del webhook
        $webhookUrl = rtrim($publicUrl, '/') . '/telegram/webhook';

        $this->info("🌐 URL pública detectada: {$publicUrl}");
        $this->info("🔗 Webhook URL: {$webhookUrl}\n");

        // Configurar el webhook
        $this->info('📡 Configurando webhook en Telegram...');

        try {
            $response = Http::withOptions([
                'verify' => env('TELEGRAM_VERIFY_SSL', true),
            ])->post("https://api.telegram.org/bot{$botToken}/setWebhook", [
                'url' => $webhookUrl,
                'secret_token' => config('services.telegram.secret_token'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['ok'] ?? false) {
                    $this->info('✅ Webhook configurado exitosamente!\n');

                    // Verificar la configuración
                    $this->info('🔍 Verificando configuración...');
                    $this->verifyWebhook($botToken);

                    return 0;
                } else {
                    $this->error('❌ Error de Telegram: ' . ($data['description'] ?? 'Error desconocido'));
                    return 1;
                }
            } else {
                $this->error('❌ Error al configurar webhook: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Excepción: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Obtiene la URL pública de ngrok desde la API local
     */
    protected function getNgrokUrl(): ?string
    {
        try {
            // Intentar obtener la URL de la API de ngrok
            $response = Http::timeout(5)->get('http://127.0.0.1:4040/api/tunnels');

            if ($response->successful()) {
                $data = $response->json();

                foreach ($data['tunnels'] ?? [] as $tunnel) {
                    // Preferir URLs https
                    if ($tunnel['proto'] === 'https') {
                        return $tunnel['public_url'];
                    }
                }

                // Si no hay https, usar http
                foreach ($data['tunnels'] ?? [] as $tunnel) {
                    return $tunnel['public_url'];
                }
            }
        } catch (\Exception $e) {
            // Ngrok no está corriendo o no responde
            Log::debug('No se pudo conectar a ngrok: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Verifica la configuración del webhook
     */
    protected function verifyWebhook(string $botToken): void
    {
        try {
            $response = Http::withOptions([
                'verify' => env('TELEGRAM_VERIFY_SSL', true),
            ])->get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");

            if ($response->successful()) {
                $data = $response->json();

                if ($data['ok'] ?? false) {
                    $info = $data['result'];

                    $this->line('');
                    $this->info('📋 Información del webhook:');
                    $this->line('  URL: ' . ($info['url'] ?? 'No configurado'));
                    $this->line('  Has custom certificate: ' . ($info['has_custom_certificate'] ? 'Sí' : 'No'));
                    $this->line('  Pending update count: ' . ($info['pending_update_count'] ?? 0));

                    if (!empty($info['last_error_date'])) {
                        $this->warn('  ⚠️ Último error: ' . date('Y-m-d H:i:s', $info['last_error_date']));
                        $this->warn('  Mensaje: ' . ($info['last_error_message'] ?? 'Sin mensaje'));
                    }

                    if (!empty($info['url'])) {
                        $this->line('');
                        $this->info('✅ El webhook está activo y listo para recibir mensajes!');
                    }
                }
            }
        } catch (\Exception $e) {
            $this->warn('⚠️ No se pudo verificar el webhook: ' . $e->getMessage());
        }
    }
}
