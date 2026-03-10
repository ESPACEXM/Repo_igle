<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Instrument;
use App\Models\Rehearsal;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class TelegramService
{
    /**
     * Token del bot de Telegram
     */
    protected ?string $botToken;

    /**
     * Endpoint de la API de Telegram
     */
    protected string $endpoint = 'https://api.telegram.org/bot';

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
    }

    /**
     * Verifica si el servicio está configurado correctamente
     */
    public function isConfigured(): bool
    {
        return !empty($this->botToken);
    }

    /**
     * Envía un mensaje de Telegram
     *
     * @param string $chatId ID del chat (número o @username)
     * @param string $message Mensaje a enviar
     * @param string $parseMode Modo de parseo (HTML o Markdown)
     * @return array Resultado del envío
     */
    public function sendMessage(string $chatId, string $message, string $parseMode = 'HTML'): array
    {
        // Rate limiting: máximo 20 mensajes por minuto por chat
        $rateLimitKey = 'telegram:' . md5($chatId);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning("TelegramService: Rate limit exceeded for chat {$chatId}");
            return [
                'success' => false,
                'message' => "Demasiados mensajes enviados. Espera {$seconds} segundos.",
            ];
        }
        RateLimiter::hit($rateLimitKey, 60);

        // Validar límite de caracteres de Telegram (4096 caracteres)
        if (strlen($message) > 4096) {
            Log::warning("TelegramService: Mensaje truncado por exceder 4096 caracteres");
            $message = substr($message, 0, 4093) . '...';
        }

        if (!$this->isConfigured()) {
            Log::error('TelegramService: Bot token no configurado');
            return [
                'success' => false,
                'message' => 'Bot token no configurado. Configure TELEGRAM_BOT_TOKEN en el archivo .env',
            ];
        }

        try {
            $url = "{$this->endpoint}{$this->botToken}/sendMessage";

            Log::info("TelegramService: Enviando mensaje a {$chatId}");

            // Configurar HTTP client - siempre verificar SSL en producción
            $httpClient = Http::withOptions([
                'verify' => true,
            ]);

            $response = $httpClient->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['ok'] ?? false) {
                    Log::info("TelegramService: Mensaje enviado exitosamente", [
                        'message_id' => $data['result']['message_id'] ?? null,
                    ]);
                    return [
                        'success' => true,
                        'message' => 'Mensaje enviado exitosamente',
                        'response' => $data,
                    ];
                }
            }

            $error = $response->json()['description'] ?? 'Error desconocido';
            Log::error("TelegramService: Error al enviar mensaje: {$error}");

            return [
                'success' => false,
                'message' => $error,
                'response' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error("TelegramService: Excepción: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envía notificación de asignación a un evento
     *
     * @param User $user Usuario a notificar
     * @param Event $event Evento asignado
     * @param Instrument $instrument Instrumento asignado
     * @return array Resultado del envío
     */
    public function sendEventAssignmentNotification(User $user, Event $event, Instrument $instrument): array
    {
        if (empty($user->telegram_chat_id)) {
            return [
                'success' => false,
                'message' => 'El usuario no tiene Telegram configurado',
            ];
        }

        $message = $this->buildAssignmentMessage($user, $event, $instrument);

        return $this->sendMessage($user->telegram_chat_id, $message);
    }

    /**
     * Envía recordatorio de evento próximo
     *
     * @param User $user Usuario a notificar
     * @param Event $event Evento próximo
     * @param Instrument $instrument Instrumento asignado
     * @return array Resultado del envío
     */
    public function sendEventReminder(User $user, Event $event, Instrument $instrument): array
    {
        if (empty($user->telegram_chat_id)) {
            return [
                'success' => false,
                'message' => 'El usuario no tiene Telegram configurado',
            ];
        }

        $message = $this->buildReminderMessage($user, $event, $instrument);

        return $this->sendMessage($user->telegram_chat_id, $message);
    }

    /**
     * Obtiene información del bot
     */
    public function getBotInfo(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Bot token no configurado',
            ];
        }

        try {
            $url = "{$this->endpoint}{$this->botToken}/getMe";
            
            $httpClient = Http::withOptions([
                'verify' => env('TELEGRAM_VERIFY_SSL', false),
            ]);
            
            $response = $httpClient->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['ok'] ?? false) {
                    return [
                        'success' => true,
                        'data' => $data['result'],
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Error al obtener información del bot',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envía un mensaje de prueba
     *
     * @param string $chatId ID del chat de prueba
     * @return array Resultado del envío
     */
    public function sendTestMessage(string $chatId): array
    {
        $message = "🧪 <b>Mensaje de Prueba</b>\n\n" .
                   "Este es un mensaje de prueba del <b>Sistema de Gestión de Alabanza</b>.\n\n" .
                   "Si recibes este mensaje, la configuración de Telegram está funcionando correctamente. ✅\n\n" .
                   "<i>Enviado desde: " . config('app.name') . "</i>";

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Construye el mensaje de asignación a evento
     */
    protected function buildAssignmentMessage(User $user, Event $event, Instrument $instrument): string
    {
        $eventDate = $event->date->format('d/m/Y');
        $eventTime = $event->date->format('h:i A');

        return "🎵 <b>¡Hola {$user->name}!</b>\n\n" .
               "Has sido asignado a un evento de alabanza.\n\n" .
               "📅 <b>Evento:</b> {$event->name}\n" .
               "🕐 <b>Fecha:</b> {$eventDate} a las {$eventTime}\n" .
               "🎸 <b>Instrumento:</b> {$instrument->name}\n\n" .
               "Por favor confirma tu asistencia en la plataforma.\n\n" .
               "¡Bendiciones! 🙏";
    }

    /**
     * Construye el mensaje de recordatorio
     */
    protected function buildReminderMessage(User $user, Event $event, Instrument $instrument): string
    {
        $eventDate = $event->date->format('d/m/Y');
        $eventTime = $event->date->format('h:i A');

        return "🎵 <b>¡Hola {$user->name}!</b>\n\n" .
               "<b>Recordatorio:</b> Mañana tienes un evento de alabanza.\n\n" .
               "📅 <b>Evento:</b> {$event->name}\n" .
               "🕐 <b>Fecha:</b> {$eventDate} a las {$eventTime}\n" .
               "🎸 <b>Instrumento:</b> {$instrument->name}\n\n" .
               "¡Te esperamos! 🙏";
    }

    /**
     * Envía mensaje a un grupo de Telegram
     *
     * @param string $groupId ID del grupo (ej: -1001234567890)
     * @param string $message Mensaje a enviar
     * @return array Resultado del envío
     */
    public function sendGroupMessage(string $groupId, string $message): array
    {
        return $this->sendMessage($groupId, $message);
    }

    /**
     * Notifica a todos los usuarios de un evento en un grupo
     *
     * @param string $groupId ID del grupo
     * @param Event $event Evento a anunciar
     * @return array Resultado del envío
     */
    public function announceEventInGroup(string $groupId, Event $event): array
    {
        $eventDate = $event->date->format('d/m/Y');
        $eventTime = $event->date->format('h:i A');

        $message = "📢 <b>Nuevo Evento de Alabanza</b>\n\n" .
                   "📅 <b>{$event->name}</b>\n" .
                   "🕐 {$eventDate} a las {$eventTime}\n\n";

        if ($event->description) {
            $message .= "📝 {$event->description}\n\n";
        }

        $message .= "¡Todos confirmados están invitados! 🙏";

        return $this->sendMessage($groupId, $message);
    }

    /**
     * Envía mensaje con botones inline para confirmación de evento
     *
     * @param string $chatId ID del chat
     * @param User $user Usuario
     * @param Event $event Evento
     * @param Instrument $instrument Instrumento asignado
     * @param bool $requiresConfirmation Si requiere confirmación (true) o solo recordatorio (false)
     * @return array Resultado del envío
     */
    public function sendEventConfirmationMessage(string $chatId, User $user, Event $event, Instrument $instrument, bool $requiresConfirmation = true): array
    {
        $eventDate = $event->date->format('d/m/Y');
        $eventTime = $event->date->format('h:i A');

        if ($requiresConfirmation) {
            $message = "🎵 <b>¡Hola {$user->name}!</b>\n\n" .
                       "Has sido asignado a un evento de alabanza.\n\n" .
                       "📅 <b>Evento:</b> {$event->name}\n" .
                       "🕐 <b>Fecha:</b> {$eventDate} a las {$eventTime}\n" .
                       "🎸 <b>Instrumento:</b> {$instrument->name}\n\n" .
                       "<b>¿Puedes asistir?</b> Por favor confirma:\n\n" .
                       "✅ <b>Sí</b> - Confirmo que puedo tocar\n" .
                       "❌ <b>No</b> - No puedo asistir (indica el motivo)\n\n" .
                       "¡Gracias! 🙏";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '✅ Sí, puedo asistir', 'callback_data' => "event:{$event->id}:confirm"],
                        ['text' => '❌ No puedo', 'callback_data' => "event:{$event->id}:decline"]
                    ]
                ]
            ];
        } else {
            $message = "🎵 <b>¡Hola {$user->name}!</b>\n\n" .
                       "<b>Recordatorio:</b> Próximo evento de alabanza.\n\n" .
                       "📅 <b>Evento:</b> {$event->name}\n" .
                       "🕐 <b>Fecha:</b> {$eventDate} a las {$eventTime}\n" .
                       "🎸 <b>Instrumento:</b> {$instrument->name}\n\n" .
                       "Este es un mensaje informativo. No requiere confirmación.\n\n" .
                       "¡Te esperamos! 🙏";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '✓ Enterado', 'callback_data' => "event:{$event->id}:ack"]
                    ]
                ]
            ];
        }

        return $this->sendMessageWithKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Envía mensaje con botones inline para confirmación de ensayo
     *
     * @param string $chatId ID del chat
     * @param User $user Usuario
     * @param Rehearsal $rehearsal Ensayo
     * @param Event $event Evento asociado
     * @return array Resultado del envío
     */
    public function sendRehearsalConfirmationMessage(string $chatId, User $user, Rehearsal $rehearsal, Event $event): array
    {
        $rehearsalDate = $rehearsal->date->format('d/m/Y');
        $rehearsalTime = $rehearsal->date->format('h:i A');
        $location = $rehearsal->location ?: 'Por confirmar';

        $message = "🎵 <b>¡Hola {$user->name}!</b>\n\n" .
                   "Tienes un ensayo programado.\n\n" .
                   "📅 <b>Fecha:</b> {$rehearsalDate} a las {$rehearsalTime}\n" .
                   "📍 <b>Lugar:</b> {$location}\n" .
                   "🎯 <b>Evento:</b> {$event->name}\n\n";

        if ($rehearsal->notes) {
            $message .= "📝 <b>Notas:</b> {$rehearsal->notes}\n\n";
        }

        $message .= "<b>¿Asistirás al ensayo?</b>\n\n" .
                    "¡Tu confirmación nos ayuda a organizarnos mejor! 🙏";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Sí asistiré', 'callback_data' => "rehearsal:{$rehearsal->id}:confirm"],
                    ['text' => '❌ No puedo', 'callback_data' => "rehearsal:{$rehearsal->id}:decline"]
                ]
            ]
        ];

        return $this->sendMessageWithKeyboard($chatId, $message, $keyboard);
    }

    /**
     * Envía mensaje con teclado inline (botones)
     *
     * @param string $chatId ID del chat
     * @param string $message Mensaje
     * @param array $keyboard Teclado inline
     * @return array Resultado del envío
     */
    public function sendMessageWithKeyboard(string $chatId, string $message, array $keyboard): array
    {
        // Rate limiting
        $rateLimitKey = 'telegram:' . md5($chatId);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 20)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning("TelegramService: Rate limit exceeded for chat {$chatId}");
            return [
                'success' => false,
                'message' => "Demasiados mensajes enviados. Espera {$seconds} segundos.",
            ];
        }
        RateLimiter::hit($rateLimitKey, 60);

        // Validar límite de caracteres
        if (strlen($message) > 4096) {
            Log::warning("TelegramService: Mensaje truncado por exceder 4096 caracteres");
            $message = substr($message, 0, 4093) . '...';
        }

        if (!$this->isConfigured()) {
            Log::error('TelegramService: Bot token no configurado');
            return [
                'success' => false,
                'message' => 'Bot token no configurado',
            ];
        }

        try {
            $url = "{$this->endpoint}{$this->botToken}/sendMessage";

            Log::info("TelegramService: Enviando mensaje con teclado a {$chatId}");

            $httpClient = Http::withOptions([
                'verify' => env('TELEGRAM_VERIFY_SSL', false),
            ]);

            $response = $httpClient->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
                'reply_markup' => json_encode($keyboard),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['ok'] ?? false) {
                    Log::info("TelegramService: Mensaje con teclado enviado exitosamente");
                    return [
                        'success' => true,
                        'message' => 'Mensaje enviado exitosamente',
                        'response' => $data,
                        'message_id' => $data['result']['message_id'] ?? null,
                    ];
                }
            }

            $error = $response->json()['description'] ?? 'Error desconocido';
            Log::error("TelegramService: Error al enviar mensaje: {$error}");

            return [
                'success' => false,
                'message' => $error,
                'response' => $response->json(),
            ];

        } catch (\Exception $e) {
            Log::error("TelegramService: Excepción: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envía mensaje solicitando justificación después de declinar
     *
     * @param string $chatId ID del chat
     * @param int $eventId ID del evento
     * @param string $type 'event' o 'rehearsal'
     * @return array Resultado del envío
     */
    public function requestDeclineReason(string $chatId, int $eventId, string $type = 'event'): array
    {
        $typeLabel = $type === 'event' ? 'al evento' : 'al ensayo';

        $message = "❌ <b>No podrás asistir {$typeLabel}</b>\n\n" .
                   "Por favor, indícanos el motivo para poder organizarnos mejor.\n\n" .
                   "Escribe tu justificación en un mensaje y la registraremos.\n\n" .
                   "<i>Ejemplos: Enfermedad, trabajo, viaje, etc.</i>";

        return $this->sendMessage($chatId, $message);
    }

    /**
     * Envía confirmación de respuesta recibida
     *
     * @param string $chatId ID del chat
     * @param string $type 'confirm' o 'decline'
     * @param string $itemType 'event' o 'rehearsal'
     * @param string $itemName Nombre del evento/ensayo
     * @return array Resultado del envío
     */
    public function sendConfirmationReceived(string $chatId, string $type, string $itemType, string $itemName): array
    {
        $actionText = $type === 'confirm' ? '✅ Confirmada' : '❌ Declinada';
        $itemLabel = $itemType === 'event' ? 'asistencia al evento' : 'asistencia al ensayo';

        $message = "{$actionText} tu {$itemLabel}:\n\n" .
                   "<b>{$itemName}</b>\n\n";

        if ($type === 'confirm') {
            $message .= "¡Gracias por confirmar! Te esperamos. 🙏";
        } else {
            $message .= "Hemos registrado tu respuesta. ¡Gracias por avisar!";
        }

        return $this->sendMessage($chatId, $message);
    }
}
