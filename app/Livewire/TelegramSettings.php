<?php

namespace App\Livewire;

use App\Services\TelegramService;
use Livewire\Component;

class TelegramSettings extends Component
{
    public $telegram_chat_id = '';
    public $telegram_username = '';
    public $testMessage = '';
    public $testSuccess = false;

    public function mount()
    {
        $user = auth()->user();
        $this->telegram_chat_id = $user->telegram_chat_id ?? '';
        $this->telegram_username = $user->telegram_username ?? '';
    }

    public function save()
    {
        $this->validate([
            'telegram_chat_id' => 'nullable|string|max:50',
            'telegram_username' => 'nullable|string|max:100|regex:/^@?[a-zA-Z0-9_]+$/',
        ]);

        $user = auth()->user();
        $user->update([
            'telegram_chat_id' => $this->telegram_chat_id ?: null,
            'telegram_username' => $this->telegram_username ?: null,
        ]);

        $this->testMessage = 'Configuración guardada correctamente. Los cambios se aplicarán la próxima vez que interactúes con el bot.';
        $this->testSuccess = true;
    }

    protected function messages()
    {
        return [
            'telegram_username.regex' => 'El nombre de usuario de Telegram solo puede contener letras, números y guiones bajos.',
        ];
    }

    public function testNotification(TelegramService $telegram)
    {
        if (empty($this->telegram_chat_id)) {
            $this->testMessage = 'Por favor ingresa tu Chat ID primero.';
            $this->testSuccess = false;
            return;
        }

        if (!$telegram->isConfigured()) {
            $this->testMessage = 'Telegram no está configurado en el sistema.';
            $this->testSuccess = false;
            return;
        }

        $result = $telegram->sendTestMessage($this->telegram_chat_id);

        if ($result['success']) {
            $this->testMessage = '¡Mensaje de prueba enviado! Revisa Telegram.';
            $this->testSuccess = true;
        } else {
            $this->testMessage = 'Error: ' . $result['message'];
            $this->testSuccess = false;
        }
    }

    public function render()
    {
        return view('livewire.telegram-settings');
    }
}
