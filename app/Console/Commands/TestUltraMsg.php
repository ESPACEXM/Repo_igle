<?php

namespace App\Console\Commands;

use App\Services\UltraMsgService;
use Illuminate\Console\Command;

class TestUltraMsg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultramsg:test {phone} {--message= : Mensaje personalizado}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un mensaje de prueba a través de UltraMsg para verificar la configuración';

    /**
     * Execute the console command.
     */
    public function handle(UltraMsgService $ultraMsg): int
    {
        $phone = $this->argument('phone');

        // Verificar configuración
        if (!$ultraMsg->isConfigured()) {
            $this->error('❌ UltraMsg no está configurado.');
            $this->line('');
            $this->line('Por favor configura las siguientes variables en tu archivo .env:');
            $this->line('  - ULTRAMSG_API_KEY');
            $this->line('  - ULTRAMSG_INSTANCE_ID');
            $this->line('');
            $this->line('Obtén tus credenciales en: https://ultramsg.com');
            return 1;
        }

        $this->info('✅ UltraMsg está configurado correctamente');
        $this->line('');

        // Obtener información de la instancia
        $this->info('Obteniendo información de la instancia...');
        $instanceInfo = $ultraMsg->getInstanceInfo();

        if ($instanceInfo['success']) {
            $this->info('✅ Conexión exitosa con UltraMsg');
            if (isset($instanceInfo['data']['name'])) {
                $this->line('   Nombre de instancia: ' . $instanceInfo['data']['name']);
            }
        } else {
            $this->warn('⚠️ No se pudo obtener información de la instancia: ' . $instanceInfo['message']);
        }

        $this->line('');

        // Enviar mensaje de prueba
        $this->info('Enviando mensaje de prueba a: ' . $phone);
        $this->line('');

        $result = $ultraMsg->sendTestMessage($phone);

        if ($result['success']) {
            $this->info('✅ Mensaje enviado exitosamente');
            if (isset($result['response']['id'])) {
                $this->line('   ID del mensaje: ' . $result['response']['id']);
            }
            return 0;
        } else {
            $this->error('❌ Error al enviar mensaje:');
            $this->error('   ' . $result['message']);
            return 1;
        }
    }
}
