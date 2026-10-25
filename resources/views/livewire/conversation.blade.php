<x-filament-panels::page>
    <div class="whatsapp-chat-container">
        <!-- Header del chat -->
        <div class="chat-header">
            <div class="user-avatar">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="user-info">
                <h2 class="user-name">Usuario {{ $userName }} - {{ $userId }}</h2>
                <p class="user-status">En línea</p>
            </div>
        </div>

        <!-- Área de mensajes -->
        <div 
            class="messages-container"
            x-data="{ scrollToBottom() { $nextTick(() => { $el.scrollTop = $el.scrollHeight }) } }"
            x-init="scrollToBottom()"
            x-on:conversation-updated.window="scrollToBottom()"
        >
            @forelse($conversation as $msg)
                @php
                    $currentDate = date('d/m/Y', is_numeric($msg['timestamp']) 
                        ? (strlen($msg['timestamp']) > 10 ? $msg['timestamp']/1000 : $msg['timestamp']) 
                        : strtotime($msg['timestamp']));
                    $prevDate = $loop->first ? null : date('d/m/Y', is_numeric($conversation[$loop->index-1]['timestamp']) 
                        ? (strlen($conversation[$loop->index-1]['timestamp']) > 10 ? $conversation[$loop->index-1]['timestamp']/1000 : $conversation[$loop->index-1]['timestamp']) 
                        : strtotime($conversation[$loop->index-1]['timestamp']));
                @endphp

                @if($loop->first || $currentDate != $prevDate)
                    <div class="date-separator">
                        <span class="date-badge">{{ $currentDate }}</span>
                    </div>
                @endif

                <div class="message-wrapper {{ $msg['fromMe'] ? 'sent' : 'received' }}">
                    <div class="message-bubble {{ $msg['fromMe'] ? 'sent' : 'received' }}">
                        <p class="message-text">{!! nl2br(e($msg['body'])) !!}</p>
                        <div class="message-info">
                            <span class="message-time">
                                {{ date('H:i', is_numeric($msg['timestamp']) 
                                    ? (strlen($msg['timestamp']) > 10 ? $msg['timestamp']/1000 : $msg['timestamp']) 
                                    : strtotime($msg['timestamp'])) }}
                            </span>
                            @if($msg['fromMe'])
                                <span class="message-status">✓✓</span>
                            @endif
                        </div>
                        <div class="bubble-tail {{ $msg['fromMe'] ? 'sent' : 'received' }}"></div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="empty-title">No hay mensajes</h3>
                    <p class="empty-subtitle">Inicia una conversación para ver los mensajes aquí</p>
                </div>
            @endforelse
        </div>

        <!-- Input de mensaje (decorativo) -->
        <div class="chat-input">
            <div class="input-wrapper">
                <span class="input-placeholder">Escribe un mensaje...</span>
            </div>
            <button class="send-button">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                </svg>
            </button>
        </div>
    </div>

    <style>
        /* Reset y contenedor principal */
        .whatsapp-chat-container * {
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Contenedor principal - Adapta al modo */
        .whatsapp-chat-container {
            width: 100% !important;
            height: 80vh !important;
            background: #ffffff !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        @media (prefers-color-scheme: dark) {
            .whatsapp-chat-container {
                background: #111b21 !important;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
            }
        }

        .dark .whatsapp-chat-container {
            background: #111b21 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        /* Header del chat - Modo claro y oscuro */
        .chat-header {
            background: #075e54 !important;
            color: white !important;
            padding: 16px 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .user-avatar {
            width: 40px !important;
            height: 40px !important;
            background: #128c7e !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        @media (prefers-color-scheme: dark) {
            .chat-header {
                background: #2a2f32 !important;
                border-bottom: 1px solid #3c4043 !important;
            }
            .user-avatar {
                background: #00a884 !important;
            }
        }

        .dark .chat-header {
            background: #2a2f32 !important;
            border-bottom: 1px solid #3c4043 !important;
        }

        .dark .user-avatar {
            background: #00a884 !important;
        }

        .user-info {
            flex: 1 !important;
        }

        .user-name {
            font-size: 16px !important;
            font-weight: 500 !important;
            color: white !important;
            margin-bottom: 2px !important;
        }

        .user-status {
            font-size: 13px !important;
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Contenedor de mensajes - Modo claro y oscuro */
        .messages-container {
            flex: 1 !important;
            overflow-y: auto !important;
            padding: 20px !important;
            /* Modo claro por defecto */
            background: linear-gradient(to bottom, #e5ddd5, #f7f3f0) !important;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="25" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/></svg>') !important;
        }

        /* Modo oscuro - detecta automáticamente */
        @media (prefers-color-scheme: dark) {
            .messages-container {
                background: linear-gradient(to bottom, #0b141a, #111b21) !important;
                background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="75" cy="25" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="25" cy="75" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.03"/></svg>') !important;
            }
        }

        /* Si Filament usa clase 'dark' en el body/html */
        .dark .messages-container {
            background: linear-gradient(to bottom, #0b141a, #111b21) !important;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="75" cy="25" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="25" cy="75" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.03"/></svg>') !important;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px !important;
        }

        .messages-container::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2) !important;
            border-radius: 3px !important;
        }

        /* Separador de fecha */
        .date-separator {
            display: flex !important;
            justify-content: center !important;
            margin: 20px 0 !important;
        }

        .date-badge {
            background: rgba(44, 54, 63, 0.9) !important;
            color: #8696a0 !important;
            padding: 6px 12px !important;
            border-radius: 15px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        }

        /* Mensajes */
        .message-wrapper {
            display: flex !important;
            margin-bottom: 8px !important;
        }

        .message-wrapper.sent {
            justify-content: flex-end !important;
        }

        .message-wrapper.received {
            justify-content: flex-start !important;
        }

        .message-bubble {
            position: relative !important;
            max-width: 65% !important;
            padding: 8px 12px 6px 12px !important;
            border-radius: 18px !important;
            word-wrap: break-word !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15) !important;
        }

        .message-bubble.sent {
            background: #005c4b !important;
            color: #e9edef !important;
            border-bottom-right-radius: 4px !important;
            margin-left: auto !important;
        }

        .message-bubble.received {
            background: #202c33 !important;
            color: #e9edef !important;
            border-bottom-left-radius: 4px !important;
            margin-right: auto !important;
        }

        .message-text {
            font-size: 14px !important;
            line-height: 1.4 !important;
            margin-bottom: 4px !important;
            color: inherit !important;
            white-space: pre-wrap !important;
            word-wrap: break-word !important;
        }

        .message-info {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 4px !important;
            margin-top: 4px !important;
        }

        .message-time {
            font-size: 11px !important;
            color: rgba(233, 237, 239, 0.6) !important;
            font-weight: 400 !important;
        }

        .message-status {
            font-size: 11px !important;
            color: #53bdeb !important;
            font-weight: bold !important;
        }

        /* Colas de las burbujas */
        .bubble-tail {
            position: absolute !important;
            width: 8px !important;
            height: 8px !important;
            bottom: 0 !important;
        }

        .bubble-tail.sent {
            right: -4px !important;
            background: #005c4b !important;
            border-bottom-right-radius: 4px !important;
            transform: rotate(45deg) !important;
            box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.3) !important;
        }

        .bubble-tail.received {
            left: -4px !important;
            background: #202c33 !important;
            border-bottom-left-radius: 4px !important;
            transform: rotate(-45deg) !important;
            box-shadow: -2px 2px 2px rgba(0, 0, 0, 0.3) !important;
        }

        /* Estado vacío */
        .empty-state {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            height: 100% !important;
            text-align: center !important;
        }

        .empty-icon {
            width: 80px !important;
            height: 80px !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 16px !important;
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .empty-title {
            font-size: 18px !important;
            font-weight: 500 !important;
            color: #8696a0 !important;
            margin-bottom: 8px !important;
        }

        .empty-subtitle {
            font-size: 14px !important;
            color: rgba(134, 150, 160, 0.7) !important;
        }

        /* Input del chat */
        .chat-input {
            background: #2a2f32 !important;
            padding: 12px 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            border-top: 1px solid #3c4043 !important;
        }

        .input-wrapper {
            flex: 1 !important;
            background: #2a2f32 !important;
            border-radius: 20px !important;
            padding: 10px 16px !important;
            border: 1px solid #3c4043 !important;
        }

        .input-placeholder {
            color: #8696a0 !important;
            font-size: 14px !important;
        }

        .send-button {
            width: 40px !important;
            height: 40px !important;
            background: #00a884 !important;
            border: none !important;
            border-radius: 50% !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: background-color 0.2s ease !important;
        }

        .send-button:hover {
            background: #008069 !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .whatsapp-chat-container {
                height: 100vh !important;
                border-radius: 0 !important;
            }

            .message-bubble {
                max-width: 80% !important;
            }

            .messages-container {
                padding: 12px !important;
            }
        }
    </style>
</x-filament-panels::page>