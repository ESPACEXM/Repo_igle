<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class UltraMsgService
{
    /**
     * API Key de UltraMsg
     */
    protected ?string $apiKey;

    /**
     * Instance ID de UltraMsg
     */
    protected ?string $instanceId;

    /**
     * Endpoint de la API de UltraMsg
     */
    protected string $endpoint = 'https://api.ultramsg.com';

    /**
     * Número máximo de reintentos
     */
    protected int $maxRetries = 3;

    public function __construct()
    {
        $this->apiKey = config('services.ultramsg.api_key', '');
        $this->instanceId = config('services.ultramsg.instance_id', '');
    }

    /**
     * Verifica si el servicio está configurado correctamente
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->instanceId);
    }

    /**
     * Envía un mensaje de WhatsApp a través de UltraMsg API
     *
     * @param string $phone Número de teléfono (con o sin formato +502)
     * @param string $message Mensaje a enviar
     * @param int $retryCount Contador de reintentos (uso interno)
     * @return array Resultado del envío ['success' => bool, 'message' => string]
     */
    public function sendMessage(string $phone, string $message, int $retryCount = 0): array
    {
        // Rate limiting: máximo 10 mensajes por minuto por número de teléfono
        $rateLimitKey = 'ultramsg:' . preg_replace('/[^0-9]/', '', $phone);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning("UltraMsgService: Rate limit exceeded for {$phone}");
            return [
                'success' => false,
                'message' => "Demasiados mensajes enviados a este número. Espera {$seconds} segundos.",
            ];
        }
        RateLimiter::hit($rateLimitKey, 60);

        // Validar longitud máxima del mensaje (WhatsApp no tiene límite estricto pero es buena práctica)
        if (strlen($message) > 4000) {
            Log::warning("UltraMsgService: Mensaje truncado por exceder 4000 caracteres");
            $message = substr($message, 0, 3997) . '...';
        }

        // Validar que tengamos credenciales configuradas
        if (!$this->isConfigured()) {
            Log::error('UltraMsgService: API Key o Instance ID no configurados');
            return [
                'success' => false,
                'message' => 'API Key o Instance ID no configurados. Configure ULTRAMSG_API_KEY y ULTRAMSG_INSTANCE_ID en el archivo .env',
            ];
        }

        // Formatear y validar el número de teléfono
        $formattedPhone = $this->formatGuatemalaPhone($phone);

        if (!$this->validateGuatemalaPhone($formattedPhone)) {
            Log::error("UltraMsgService: Número de teléfono inválido: {$phone}");
            return [
                'success' => false,
                'message' => "Número de teléfono inválido: {$phone}. Formato requerido: +502XXXXXXXX",
            ];
        }

        try {
            // Construir URL de la API
            $url = "{$this->endpoint}/{$this->instanceId}/messages/chat";

            Log::info("UltraMsgService: Enviando mensaje a {$formattedPhone}");

            // Realizar la petición HTTP POST
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'token' => $this->apiKey,
                'to' => $formattedPhone,
                'body' => $message,
            ]);

            // Verificar respuesta
            if ($response->successful()) {
                $responseData = $response->json();

                // UltraMsg devuelve sent: true cuando el mensaje se envió
                if (isset($responseData['sent']) && $responseData['sent'] === true) {
                    Log::info("UltraMsgService: Mensaje enviado exitosamente a {$formattedPhone}", [
                        'message_id' => $responseData['id'] ?? null,
                    ]);
                    return [
                        'success' => true,
                        'message' => 'Mensaje enviado exitosamente',
                        'response' => $responseData,
                    ];
                }

                // Si hay error en la respuesta
                if (isset($responseData['error'])) {
                    Log::error("UltraMsgService: Error en respuesta de API: " . $responseData['error']);
                    return [
                        'success' => false,
                        'message' => 'Error en respuesta de API: ' . $responseData['error'],
                        'response' => $responseData,
                    ];
                }

                Log::warning("UltraMsgService: Respuesta inesperada", $responseData);
                return [
                    'success' => false,
                    'message' => 'Respuesta inesperada de la API',
                    'response' => $responseData,
                ];
            }

            // Error HTTP - reintentar si es error 5xx
            if ($retryCount < $this->maxRetries && $response->status() >= 500) {
                Log::warning("UltraMsgService: Error HTTP {$response->status()}, reintentando...");
                sleep(2 ** $retryCount); // Espera exponencial
                return $this->sendMessage($phone, $message, $retryCount + 1);
            }

            Log::error("UltraMsgService: Error HTTP {$response->status()}", [
                'body' => $response->body(),
            ]);
            return [
                'success' => false,
                'message' => "Error HTTP {$response->status()}: {$response->body()}",
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error de conexión - reintentar si es posible
            if ($retryCount < $this->maxRetries) {
                Log::warning("UltraMsgService: Error de conexión, reintentando... (intento {$retryCount})");
                sleep(2 ** $retryCount);
                return $this->sendMessage($phone, $message, $retryCount + 1);
            }

            Log::error("UltraMsgService: Error de conexión después de {$this->maxRetries} intentos: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error("UltraMsgService: Excepción inesperada: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
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
        if (empty($user->phone)) {
            return [
                'success' => false,
                'message' => 'El usuario no tiene número de teléfono registrado',
            ];
        }

        $message = $this->buildAssignmentMessage($user, $event, $instrument);

        return $this->sendMessage($user->phone, $message);
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
        if (empty($user->phone)) {
            return [
                'success' => false,
                'message' => 'El usuario no tiene número de teléfono registrado',
            ];
        }

        $message = $this->buildReminderMessage($user, $event, $instrument);

        return $this->sendMessage($user->phone, $message);
    }

    /**
     * Obtiene información de la instancia (útil para verificar configuración)
     */
    public function getInstanceInfo(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Servicio no configurado',
            ];
        }

        try {
            $url = "{$this->endpoint}/{$this->instanceId}/instance/me";

            $response = Http::get($url, [
                'token' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Error al obtener información: ' . $response->body(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envía un mensaje de prueba (útil para verificar la configuración)
     *
     * @param string $phone Número de teléfono de prueba
     * @return array Resultado del envío
     */
    public function sendTestMessage(string $phone): array
    {
        $message = "🧪 *Mensaje de Prueba - Sistema de Alabanza*\n\n" .
                   "Este es un mensaje de prueba del Sistema de Gestión de Alabanza.\n\n" .
                   "Si recibes este mensaje, la configuración de WhatsApp con UltraMsg está funcionando correctamente. ✅\n\n" .
                   "_Enviado desde: " . config('app.name') . "_";

        return $this->sendMessage($phone, $message);
    }

    /**
     * Formatea un número de teléfono al formato Guatemala (+502XXXXXXXX)
     *
     * @param string $phone Número de teléfono en cualquier formato
     * @return string Número formateado
     */
    public function formatGuatemalaPhone(string $phone): string
    {
        // Remover todos los caracteres no numéricos
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Si ya tiene el prefijo 502, asegurarse de que tenga el +
        if (str_starts_with($digits, '502')) {
            // Si tiene 11 dígitos (502 + 8 dígitos), está completo
            if (strlen($digits) === 11) {
                return '+' . $digits;
            }
        }

        // Si tiene 8 dígitos (sin prefijo), agregar +502
        if (strlen($digits) === 8) {
            return '+502' . $digits;
        }

        // Si no cumple con los formatos esperados, retornar como está
        // (para que la validación lo rechace)
        return !str_starts_with($digits, '+') ? '+' . $digits : $digits;
    }

    /**
     * Valida que un número de teléfono tenga formato Guatemala válido (+502XXXXXXXX)
     *
     * @param string $phone Número de teléfono a validar
     * @return bool True si es válido
     */
    public function validateGuatemalaPhone(string $phone): bool
    {
        // Debe comenzar con +502 seguido de 8 dígitos
        $pattern = '/^\+502\d{8}$/';
        return preg_match($pattern, $phone) === 1;
    }

    /**
     * Construye el mensaje de asignación a evento
     */
    protected function buildAssignmentMessage(User $user, Event $event, Instrument $instrument): string
    {
        $eventDate = $event->date->format('d/m/Y');
        $eventTime = $event->date->format('h:i A');

        return "¡Hola {$user->name}! 🎵\n\n" .
               "Has sido asignado a un evento de alabanza.\n\n" .
               "📅 *Evento:* {$event->name}\n" .
               "🕐 *Fecha:* {$eventDate} a las {$eventTime}\n" .
               "🎸 *Instrumento:* {$instrument->name}\n\n" .
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

        return "¡Hola {$user->name}! 🎵\n\n" .
               "*Recordatorio:* Mañana tienes un evento de alabanza.\n\n" .
               "📅 *Evento:* {$event->name}\n" .
               "🕐 *Fecha:* {$eventDate} a las {$eventTime}\n" .
               "🎸 *Instrumento:* {$instrument->name}\n\n" .
               "¡Te esperamos! 🙏";
    }
}
