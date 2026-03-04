<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Instrument;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * API Key de CallMeBot
     */
    protected ?string $apiKey;

    /**
     * Endpoint de la API de CallMeBot
     */
    protected string $endpoint = 'https://api.callmebot.com/whatsapp.php';

    /**
     * Número máximo de reintentos
     */
    protected int $maxRetries = 3;

    public function __construct()
    {
        $this->apiKey = config('services.whatsapp.callmebot_api_key', '');
    }

    /**
     * Envía un mensaje de WhatsApp a través de CallMeBot API
     *
     * @param string $phone Número de teléfono (con o sin formato +502)
     * @param string $message Mensaje a enviar
     * @param int $retryCount Contador de reintentos (uso interno)
     * @return array Resultado del envío ['success' => bool, 'message' => string]
     */
    public function sendMessage(string $phone, string $message, int $retryCount = 0): array
    {
        // Validar que tengamos API key configurada
        if (empty($this->apiKey)) {
            Log::error('WhatsAppService: API Key no configurada');
            return [
                'success' => false,
                'message' => 'API Key no configurada. Configure CALLMEBOT_API_KEY en el archivo .env',
            ];
        }

        // Formatear y validar el número de teléfono
        $formattedPhone = $this->formatGuatemalaPhone($phone);
        
        if (!$this->validateGuatemalaPhone($formattedPhone)) {
            Log::error("WhatsAppService: Número de teléfono inválido: {$phone}");
            return [
                'success' => false,
                'message' => "Número de teléfono inválido: {$phone}. Formato requerido: +502XXXXXXXX",
            ];
        }

        try {
            // Codificar el mensaje para URL
            $encodedMessage = urlencode($message);

            // Construir URL de la API
            $url = "{$this->endpoint}?phone={$formattedPhone}&text={$encodedMessage}&apikey={$this->apiKey}";

            Log::info("WhatsAppService: Enviando mensaje a {$formattedPhone}");

            // Realizar la petición HTTP
            $response = Http::timeout(30)->get($url);

            // Verificar respuesta
            if ($response->successful()) {
                $responseBody = $response->body();
                
                // CallMeBot devuelve mensajes de éxito o error en el cuerpo
                if (str_contains(strtolower($responseBody), 'success') || 
                    str_contains(strtolower($responseBody), 'queued') ||
                    str_contains(strtolower($responseBody), 'enviado')) {
                    Log::info("WhatsAppService: Mensaje enviado exitosamente a {$formattedPhone}");
                    return [
                        'success' => true,
                        'message' => 'Mensaje enviado exitosamente',
                        'response' => $responseBody,
                    ];
                }

                // Si la respuesta indica error pero es recuperable, reintentar
                if ($retryCount < $this->maxRetries && $this->isRetryableError($responseBody)) {
                    Log::warning("WhatsAppService: Error recuperable, reintentando... (intento {$retryCount})");
                    sleep(2 ** $retryCount); // Espera exponencial: 1s, 2s, 4s
                    return $this->sendMessage($phone, $message, $retryCount + 1);
                }

                Log::error("WhatsAppService: Error en respuesta de API: {$responseBody}");
                return [
                    'success' => false,
                    'message' => 'Error en respuesta de API: ' . $responseBody,
                    'response' => $responseBody,
                ];
            }

            // Error HTTP
            if ($retryCount < $this->maxRetries && $response->status() >= 500) {
                Log::warning("WhatsAppService: Error HTTP {$response->status()}, reintentando...");
                sleep(2 ** $retryCount);
                return $this->sendMessage($phone, $message, $retryCount + 1);
            }

            Log::error("WhatsAppService: Error HTTP {$response->status()} - {$response->body()}");
            return [
                'success' => false,
                'message' => "Error HTTP {$response->status()}: {$response->body()}",
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Error de conexión - reintentar si es posible
            if ($retryCount < $this->maxRetries) {
                Log::warning("WhatsAppService: Error de conexión, reintentando... (intento {$retryCount})");
                sleep(2 ** $retryCount);
                return $this->sendMessage($phone, $message, $retryCount + 1);
            }

            Log::error("WhatsAppService: Error de conexión después de {$this->maxRetries} intentos: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error("WhatsAppService: Excepción inesperada: " . $e->getMessage());
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
               "📅 Evento: {$event->name}\n" .
               "🕐 Fecha: {$eventDate} a las {$eventTime}\n" .
               "🎸 Instrumento: {$instrument->name}\n\n" .
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
               "Recordatorio: Mañana tienes un evento de alabanza.\n\n" .
               "📅 Evento: {$event->name}\n" .
               "🕐 Fecha: {$eventDate} a las {$eventTime}\n" .
               "🎸 Instrumento: {$instrument->name}\n\n" .
               "¡Te esperamos! 🙏";
    }

    /**
     * Determina si un error es recuperable y debe reintentarse
     */
    protected function isRetryableError(string $response): bool
    {
        $retryablePatterns = [
            'timeout',
            'rate limit',
            'too many requests',
            'temporarily unavailable',
            'service unavailable',
            'gateway',
        ];

        $lowerResponse = strtolower($response);
        foreach ($retryablePatterns as $pattern) {
            if (str_contains($lowerResponse, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Envía un mensaje de prueba (útil para verificar la configuración)
     *
     * @param string $phone Número de teléfono de prueba
     * @return array Resultado del envío
     */
    public function sendTestMessage(string $phone): array
    {
        $message = "🧪 *Mensaje de Prueba*\n\n" .
                   "Este es un mensaje de prueba del Sistema de Gestión de Alabanza.\n\n" .
                   "Si recibes este mensaje, la configuración de WhatsApp está funcionando correctamente. ✅";

        return $this->sendMessage($phone, $message);
    }
}
