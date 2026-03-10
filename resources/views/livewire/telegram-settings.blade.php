<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.03-1.99 1.27-5.62 3.72-.53.36-1.01.54-1.44.53-.47-.01-1.38-.27-2.06-.49-.83-.27-1.49-.42-1.43-.88.03-.24.37-.49 1.02-.74 4-1.74 6.67-2.89 8.02-3.46 3.82-1.6 4.61-1.88 5.13-1.89.11 0 .37.03.53.17.14.12.18.28.2.45-.01.07-.01.24-.02.38z"/>
        </svg>
        Configuración de Telegram
    </h2>

    <p class="text-gray-600 mb-6">
        Configura tu cuenta de Telegram para recibir notificaciones de eventos y ensayos.
    </p>

    @if ($testMessage)
        <div class="mb-4 p-4 rounded-lg {{ $testSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $testMessage }}
        </div>
    @endif

    <div class="space-y-4">
        <div>
            <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700 mb-1">
                Chat ID de Telegram
            </label>
            <input 
                type="text" 
                id="telegram_chat_id"
                wire:model="telegram_chat_id"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Ej: 123456789"
            >
            <p class="mt-1 text-xs text-gray-500">
                Obtén tu Chat ID iniciando una conversación con tu bot y visitando: 
                <code class="bg-gray-100 px-1 rounded">https://api.telegram.org/bot[TU_TOKEN]/getUpdates</code>
            </p>
        </div>

        <div>
            <label for="telegram_username" class="block text-sm font-medium text-gray-700 mb-1">
                Username de Telegram (opcional)
            </label>
            <input 
                type="text" 
                id="telegram_username"
                wire:model="telegram_username"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="Ej: @miusuario"
            >
        </div>

        <div class="flex gap-3 pt-4">
            <button 
                wire:click="save"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
                Guardar Configuración
            </button>
            
            <button 
                wire:click="testNotification"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
            >
                Enviar Mensaje de Prueba
            </button>
        </div>
    </div>

    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
        <h3 class="font-medium text-blue-900 mb-2">¿Cómo obtener tu Chat ID?</h3>
        <ol class="list-decimal list-inside text-sm text-blue-800 space-y-1">
            <li>Abre Telegram y busca tu bot: <strong>@mi_iglesia_bot</strong></li>
            <li>Envía el comando <code class="bg-white px-1 rounded">/start</code></li>
            <li>Visita la URL que aparece arriba reemplazando [TU_TOKEN]</li>
            <li>Busca el número en <code class="bg-white px-1 rounded">"chat":{"id":123456789</code></li>
        </ol>
    </div>
</div>
