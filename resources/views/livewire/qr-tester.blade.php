<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount};

state([
    'connectionStatus' => 'desconocido',
    'lastMessage' => null,
    'qr' => null,
]);
mount(function () {
    if (!auth()->user()?->botname) {
        $this->message = 'Debes crear un bot para poder usar esta funcionalidad';
        $this->status = 'disconnected';
        return;
    }
    $getStatus = new ApiService(auth()->user()->botname, 'whatsapp/status');
    $status = $getStatus->fetch();
    $this->connectionStatus = $status['connected'] ? 'conectado' : 'desconectado';

    $api = new ApiService(auth()->user()->botname, 'whatsapp/qr');
    $data = $api->fetch();
    if (!$data) return;
    $this->qr = $data['qr'];
});
?>

<div>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <div x-data x-init="
        const botname = $el.dataset.botname;
        const socket = io('https://api.aibotia.com', {
            path: `/${botname}/socket.io`,
            transports: ['websocket'],
            forceNew: true
        });

        socket.on('connect', () => {
            console.log('✅ Conectado al WebSocket');
        });

        socket.on('connect_error', (error) => {
            console.error('❌ Error:', error);
        });

        socket.on('disconnect', () => {
            console.warn('❌ Desconectado');
        });

        socket.on('qr', (data) => {
            const status = data['status'] == 'waiting' ? 'desconectado' : 'Conectado';
            console.log('📥 QR recibido:', data);
            $wire.set('connectionStatus', status);
            $wire.set('lastMessage', data);
        });
    " data-botname="{{ auth()->user()->botname }}">
        <div class="text-sm font-mono">
            <p>🔌 Estado: <span class="font-bold">{{ $connectionStatus }}</span></p>
            @if ($lastMessage && isset($lastMessage['data']['qrDataUrl']))
            <p>✅ Imagen QR decodificada:</p>
            <img src="{{ $lastMessage['data']['qrDataUrl'] }}" alt="QR" class="w-64 mx-auto">
            @else
            @if(!empty($qr))
            <img src="{{ $qr }}" alt="QR" class="w-64 mx-auto">
            @else
            <p>Esperar que se muestre QR</p>
            @endif
            @endif
        </div>
    </div>
</div>