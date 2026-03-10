<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TestTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {chat_id} {--message= : Mensaje personalizado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un mensaje de prueba a través de Telegram para verificar la configuración';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram): int
    {
        $chatId = $this->argument('chat_id');

        // Verificar configuración
        if (!$telegram->isConfigured()) {
            $this->error('❌ Telegram no está configurado.');
            $this->line('');
            $this->line('Por favor configura las siguientes variables en tu archivo .env:');
            $this->line('  - TELEGRAM_BOT_TOKEN');
            $this->line('');
            $this->line('Crea un bot con @BotFather en Telegram y obtén tu token.');
            return 1;
        }

        $this->info('✅ Telegram está configurado correctamente');
        $this->line('');

        // Obtener información del bot
        $this->info('Obteniendo información del bot...');
        $botInfo = $telegram->getBotInfo();

        if ($botInfo['success']) {
            $this->info('✅ Conexión exitosa con Telegram');
            $this->line('   Nombre del bot: @' . $botInfo['data']['username']);
            $this->line('   Nombre: ' . $botInfo['data']['first_name']);
        } else {
            $this->warn('⚠️ No se pudo obtener información del bot: ' . $botInfo['message']);
        }

        $this->line('');

        // Enviar mensaje de prueba
        $this->info('Enviando mensaje de prueba a: ' . $chatId);
        $this->line('');

        $result = $telegram->sendTestMessage($chatId);

        if ($result['success']) {
            $this->info('✅ Mensaje enviado exitosamente');
            if (isset($result['response']['result']['message_id'])) {
                $this->line('   ID del mensaje: ' . $result['response']['result']['message_id']);
            }
            return 0;
        } else {
            $this->error('❌ Error al enviar mensaje:');
            $this->error('   ' . $result['message']);
            $this->line('');
            $this->line('Nota: Asegúrate de que:');
            $this->line('  1. El usuario haya iniciado el bot con /start');
            $this->line('  2. El chat_id sea correcto');
            return 1;
        }
    }
}
