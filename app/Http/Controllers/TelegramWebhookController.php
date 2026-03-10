<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rehearsal;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Maneja los webhooks de Telegram
     */
    public function handle(Request $request, TelegramService $telegram): JsonResponse
    {
        // Verificar token secreto para autenticar que la solicitud viene de Telegram
        $secretToken = config('services.telegram.secret_token');
        if ($secretToken) {
            $requestToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($requestToken !== $secretToken) {
                Log::warning('Intento de acceso no autorizado al webhook de Telegram');
                return response()->json(['status' => 'unauthorized'], 401);
            }
        }

        $data = $request->all();

        Log::info('Telegram Webhook recibido', $data);

        // Verificar si es un callback query (botón presionado)
        if (isset($data['callback_query'])) {
            return $this->handleCallbackQuery($data['callback_query'], $telegram);
        }

        // Verificar si es un mensaje de texto
        if (isset($data['message'])) {
            return $this->handleMessage($data['message'], $telegram);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Maneja los botones inline presionados
     */
    protected function handleCallbackQuery(array $callbackQuery, TelegramService $telegram): JsonResponse
    {
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $callbackData = $callbackQuery['data'] ?? '';
        $userTelegramId = $callbackQuery['from']['id'] ?? null;

        if (!$chatId || !$callbackData) {
            Log::error('Datos incompletos en callback query', $callbackQuery);
            return response()->json(['status' => 'error', 'message' => 'Datos incompletos']);
        }

        // Parsear el callback data: "event:123:confirm" o "rehearsal:456:decline"
        $parts = explode(':', $callbackData);
        if (count($parts) !== 3) {
            Log::error('Formato de callback data inválido', ['data' => $callbackData]);
            return response()->json(['status' => 'error', 'message' => 'Formato inválido']);
        }

        [$type, $id, $action] = $parts;
        $id = (int) $id;

        // Buscar el usuario por telegram_chat_id
        $user = User::where('telegram_chat_id', $chatId)->first();

        // Verificar que el usuario que hizo clic sea el mismo usuario del chat
        // (Importante para seguridad en grupos)
        if ($userTelegramId && $user) {
            // En chats privados, el chat_id y el user_id son iguales
            // Pero en grupos pueden ser diferentes, verificamos si tenemos el telegram_id del usuario
            if (isset($user->telegram_id) && $userTelegramId != $user->telegram_id) {
                Log::warning('Usuario de Telegram no coincide', [
                    'callback_user_id' => $userTelegramId,
                    'stored_telegram_id' => $user->telegram_id ?? null,
                    'chat_id' => $chatId
                ]);
            }
        }

        if (!$user) {
            Log::error('Usuario no encontrado', ['chat_id' => $chatId]);
            $telegram->sendMessage($chatId, '❌ No se encontró tu usuario en el sistema. Por favor contacta al administrador.');
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado']);
        }

        // Procesar según el tipo
        if ($type === 'event') {
            return $this->handleEventConfirmation($user, $id, $action, $chatId, $telegram);
        }

        if ($type === 'rehearsal') {
            return $this->handleRehearsalConfirmation($user, $id, $action, $chatId, $telegram);
        }

        return response()->json(['status' => 'error', 'message' => 'Tipo desconocido']);
    }

    /**
     * Maneja la confirmación de evento
     */
    protected function handleEventConfirmation(User $user, int $eventId, string $action, string $chatId, TelegramService $telegram): JsonResponse
    {
        $event = Event::find($eventId);

        if (!$event) {
            $telegram->sendMessage($chatId, '❌ El evento ya no existe.');
            return response()->json(['status' => 'error', 'message' => 'Evento no encontrado']);
        }

        // Verificar si el usuario está asignado al evento
        $pivot = $event->users()->where('user_id', $user->id)->first();

        if (!$pivot) {
            $telegram->sendMessage($chatId, '❌ No estás asignado a este evento.');
            return response()->json(['status' => 'error', 'message' => 'Usuario no asignado']);
        }

        // Verificar si ya respondió
        if ($pivot->pivot->status !== 'pending' && $pivot->pivot->responded_at) {
            $telegram->sendMessage($chatId, '⚠️ Ya has respondido a este evento anteriormente.');
            return response()->json(['status' => 'error', 'message' => 'Ya respondido']);
        }

        if ($action === 'confirm') {
            // Confirmar asistencia
            $event->users()->updateExistingPivot($user->id, [
                'status' => 'confirmed',
                'responded_at' => now(),
            ]);

            $telegram->sendConfirmationReceived($chatId, 'confirm', 'event', $event->name);
            Log::info("Usuario {$user->id} confirmó asistencia al evento {$eventId}");

            return response()->json(['status' => 'success', 'action' => 'confirmed']);
        }

        if ($action === 'decline') {
            // Solicitar justificación
            // Guardar estado temporal para esperar justificación
            cache()->put("decline_reason:{$user->id}:event:{$eventId}", true, now()->addHours(24));

            // Guardar que el usuario declinó y esperar justificación
            $event->users()->updateExistingPivot($user->id, [
                'status' => 'declined',
                'declined_at' => now(),
            ]);

            $telegram->requestDeclineReason($chatId, $eventId, 'event');
            Log::info("Usuario {$user->id} declinó evento {$eventId}, esperando justificación");

            return response()->json(['status' => 'success', 'action' => 'decline_requested']);
        }

        if ($action === 'ack') {
            // Solo marcar como enterado (para recordatorios informativos)
            $event->users()->updateExistingPivot($user->id, [
                'responded_at' => now(),
            ]);

            $telegram->sendMessage($chatId, "✅ Hemos registrado que viste el mensaje del evento \"{$event->name}\".");
            Log::info("Usuario {$user->id} marcó como enterado el evento {$eventId}");

            return response()->json(['status' => 'success', 'action' => 'acknowledged']);
        }

        return response()->json(['status' => 'error', 'message' => 'Acción desconocida']);
    }

    /**
     * Maneja la confirmación de ensayo
     */
    protected function handleRehearsalConfirmation(User $user, int $rehearsalId, string $action, string $chatId, TelegramService $telegram): JsonResponse
    {
        $rehearsal = Rehearsal::find($rehearsalId);

        if (!$rehearsal) {
            $telegram->sendMessage($chatId, '❌ El ensayo ya no existe.');
            return response()->json(['status' => 'error', 'message' => 'Ensayo no encontrado']);
        }

        $event = $rehearsal->event;

        if (!$event) {
            $telegram->sendMessage($chatId, '❌ El evento asociado ya no existe.');
            return response()->json(['status' => 'error', 'message' => 'Evento no encontrado']);
        }

        // Verificar si el usuario está asignado al evento (por ende al ensayo)
        $pivot = $event->users()->where('user_id', $user->id)->first();

        if (!$pivot) {
            $telegram->sendMessage($chatId, '❌ No estás asignado a este ensayo.');
            return response()->json(['status' => 'error', 'message' => 'Usuario no asignado']);
        }

        if ($action === 'confirm') {
            // Crear o actualizar asistencia al ensayo
            $rehearsal->attendances()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status' => 'present',
                    'responded_at' => now(),
                ]
            );

            $telegram->sendConfirmationReceived($chatId, 'confirm', 'rehearsal', $event->name);
            Log::info("Usuario {$user->id} confirmó asistencia al ensayo {$rehearsalId}");

            return response()->json(['status' => 'success', 'action' => 'confirmed']);
        }

        if ($action === 'decline') {
            // Solicitar justificación
            cache()->put("decline_reason:{$user->id}:rehearsal:{$rehearsalId}", true, now()->addHours(24));

            $telegram->requestDeclineReason($chatId, $rehearsalId, 'rehearsal');
            Log::info("Usuario {$user->id} declinó ensayo {$rehearsalId}, esperando justificación");

            return response()->json(['status' => 'success', 'action' => 'decline_requested']);
        }

        return response()->json(['status' => 'error', 'message' => 'Acción desconocida']);
    }

    /**
     * Maneja mensajes de texto (para justificaciones)
     */
    protected function handleMessage(array $message, TelegramService $telegram): JsonResponse
    {
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        if (!$chatId) {
            return response()->json(['status' => 'error', 'message' => 'Chat ID no encontrado']);
        }

        $user = User::where('telegram_chat_id', $chatId)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Usuario no encontrado']);
        }

        // Verificar si hay solicitudes de justificación pendientes para eventos
        $pendingEvents = \Illuminate\Support\Facades\DB::table('event_user')
            ->where('user_id', $user->id)
            ->where('status', 'declined')
            ->whereNull('decline_reason')
            ->where(function($query) {
                $query->whereNull('declined_at')
                      ->orWhere('declined_at', '>=', now()->subHours(24));
            })
            ->get();

        // Verificar si hay solicitudes de justificación pendientes para ensayos
        $pendingRehearsals = \Illuminate\Support\Facades\DB::table('attendances')
            ->where('user_id', $user->id)
            ->where('status', 'declined')
            ->whereNull('decline_reason')
            ->where(function($query) {
                $query->whereNull('declined_at')
                      ->orWhere('declined_at', '>=', now()->subHours(24));
            })
            ->get();

        // Si no hay solicitudes pendientes, enviar ayuda
        if ($pendingEvents->isEmpty() && $pendingRehearsals->isEmpty()) {
            // Solo procesar comandos已知
            if ($text === '/start' || $text === '/help') {
                $helpMessage = "🎵 <b>Sistema de Gestión de Alabanza</b>\n\n" .
                              "Los comandos disponibles son:\n" .
                              "/start - Iniciar conversación\n" .
                              "/help - Mostrar esta ayuda\n\n" .
                              "Las confirmaciones de eventos y ensayos se hacen mediante los botones que recibirás.";
                $telegram->sendMessage($chatId, $helpMessage);
            }
            return response()->json(['status' => 'ignored']);
        }

        // Procesar justificación para evento
        if ($pendingEvents->isNotEmpty()) {
            $eventUser = $pendingEvents->first();
            $event = Event::find($eventUser->event_id);
            
            if ($event) {
                // Sanitizar justificación para prevenir XSS
                $sanitizedReason = strip_tags($text);
                
                // Guardar justificación
                \Illuminate\Support\Facades\DB::table('event_user')
                    ->where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->update([
                        'decline_reason' => $sanitizedReason,
                        'status' => 'declined',
                    ]);

                $telegram->sendMessage($chatId, "✅ Gracias. Tu justificación \"{$sanitizedReason}\" ha sido registrada para el evento \"{$event->name}\". Un líder del ministry te contactará pronto.");
                Log::info("Usuario {$user->id} envió justificación para evento {$event->id}: {$sanitizedReason}");
            }
        }

        // Procesar justificación para ensayo
        if ($pendingRehearsals->isNotEmpty()) {
            $attendance = $pendingRehearsals->first();
            $rehearsal = Rehearsal::find($attendance->rehearsal_id);
            
            if ($rehearsal) {
                // Sanitizar justificación para prevenir XSS
                $sanitizedReason = strip_tags($text);
                
                // Guardar justificación
                \Illuminate\Support\Facades\DB::table('attendances')
                    ->where('user_id', $user->id)
                    ->where('rehearsal_id', $rehearsal->id)
                    ->update([
                        'decline_reason' => $sanitizedReason,
                    ]);

                $telegram->sendMessage($chatId, "✅ Gracias. Tu justificación \"{$sanitizedReason}\" ha sido registrada para el ensayo.");
                Log::info("Usuario {$user->id} envió justificación para ensayo {$rehearsal->id}: {$sanitizedReason}");
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Justificación procesada']);
    }
}
