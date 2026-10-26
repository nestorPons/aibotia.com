<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount};

state([
    'connectionStatus' => 'desconocido',
    'lastMessage' => null,
    'qr' => null,
    'botActive' => true, // <-- estado del switch
    'onlyAdmins' => false, // <-- estado del switch

]);
mount(function () {
    if (!auth()->user()?->botname) {
        $this->message = 'Debes crear un bot para poder usar esta funcionalidad';
        $this->status = 'disconnected';
        return;
    }
    $getStatus = new ApiService(auth()->user()->botname, 'whatsapp/status');
    $status = $getStatus->fetch();
    $this->connectionStatus = !empty($status['connected']) ? 'vinculado' : 'desvinculado';

    $api = new ApiService(auth()->user()->botname, 'whatsapp/qr');
    $data = $api->fetch();
    if (!$data) return;
    $this->qr = $data['qr'];

    // Estado del BOT (ON/OFF)
    $botStatus = new ApiService(auth()->user()->botname, 'assistant/status');
    $data = $botStatus->fetch();
    if ($data && isset($data['active'])) {
        $this->botActive = $data['active'];
    }
    if ($data && isset($data['onlyAdmins'])) {
        $this->onlyAdmins = $data['onlyAdmins'];
    }
});

$toggleBot = function () {
    $newStatus = !$this->botActive;
    $api = new ApiService(auth()->user()->botname, 'assistant');
    $response = $api->update('status', ['active' => $newStatus]); // PUT
    if ($response && isset($response['success'])) {
        $this->botActive = $newStatus;
        \Filament\Notifications\Notification::make()
            ->title('Estado del bot actualizado')
            ->success()
            ->send();
    } else {
        \Filament\Notifications\Notification::make()
            ->title('Error al cambiar el estado del bot')
            ->danger()
            ->send();
    }
};

$toggleOnlyAdmins = function () {
    $newStatus = !$this->onlyAdmins;
    $api = new ApiService(auth()->user()->botname, 'assistant');
    $response = $api->update('only-admins', ['onlyAdmins' => $newStatus]); // PUT
    if ($response && isset($response['success'])) {
        $this->onlyAdmins = $newStatus;
        \Filament\Notifications\Notification::make()
            ->title('Estado del bot actualizado')
            ->success()
            ->send();
    } else {
        \Filament\Notifications\Notification::make()
            ->title('Error al cambiar el estado del bot')
            ->danger()
            ->send();
    }
};

$disconnectWhatsapp = function () {
    $response = new ApiService(auth()->user()->botname, 'whatsapp/disconnect');
    if (!$response) return;
    $this->connectionStatus = 'desvinculado';
};

$resetThreads = function () {
    $api = new ApiService(auth()->user()->botname, 'users');
    $response = $api->delete();
 
    if ($response && isset($response['success'])) {
        \Filament\Notifications\Notification::make()
            ->title('Hilos reseteados correctamente.')
            ->success()
            ->send();
    } else {
        \Filament\Notifications\Notification::make()
            ->title('Error al resetear los hilos.')
            ->danger()
            ->send();
    }
};
$loadUsers = function () {
    $api = new ApiService(auth()->user()->botname, 'users');
    if (!auth()->user()?->botname) {
        $this->message = 'Debes crear un bot para poder usar esta funcionalidad';
        $this->status = 'danger';
        return;
    }
    $response = $api->importUsers();
    if (!$response || isset($response['error'])) {
        $this->message = 'Error al cargar los usuarios. Asegúrate de que tu bot está activo.';
        $this->status = 'danger';
        return;
    }

    if ($response->successful()) {
        \Filament\Notifications\Notification::make()
            ->title('Usuarios cargados correctamente.')
            ->success()
            ->send();
    } else {
        \Filament\Notifications\Notification::make()
            ->title('Error al cargar los usuarios.')
            ->danger()
            ->send();
    }
};
?>

<div>
    <style>
        .disconnect-button {
            margin-top: 1rem;
            background-color: #dc2626;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            border: none;
            cursor: pointer;
        }

        input:checked+.block {
            background-color: #22c55e;
            /* verde */
        }

        input:checked+.block .dot {
            transform: translateX(100%);
        }
    </style>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <div x-data x-init="
        const botname = $el.dataset.botname;
        const socket = io('https://api.aibotia.com', {
            path: `/${botname}/socket.io`,
            transports: ['websocket'],
            forceNew: true
        });

        socket.on('connect', () => {
            console.log('✅ vinculado al WebSocket');
        });

        socket.on('connect_error', (error) => {
            console.error('❌ Error:', error);
        });

        socket.on('disconnect', () => {
            console.warn('❌ desvinculado');
        });

        socket.on('qr', (data) => {
            const status = data['status'] == 'waiting' ? 'Desvinculado' : 'Vinculado';
            console.log('📥 QR recibido:', data);
            $wire.set('connectionStatus', status);
            $wire.set('lastMessage', data);
        });
    " data-botname="{{ auth()->user()->botname }}">

        <div class="my-4 mx-4">
            <x-filament::button color="primary" wire:click="loadUsers()" style="display: flex; align-items: center; flex-wrap: nowrap;">
                <x-heroicon-s-users class="w-5 h-5 mr-1" />
                <span class="w-5">Cargar usuarios</span>
            </x-filament::button>
        </div>

        <div class="my-4 mx-4">
            <x-filament::button color="warning" wire:click="resetThreads()" 
            style="display: flex; align-items: center; flex-wrap: nowrap;">
                <x-heroicon-s-users class="w-5 h-5 mr-1" />
                <span class="w-5">Resetear hilos</span>
            </x-filament::button>
        </div>
        <div class="flex items-center my-4 mx-4 gap-2">
            <div class="switch-container my-4 mx-4 ">
                <input type="checkbox"
                    wire:click="toggleBot"
                    class="switch-input"
                    id="switch-bot-active"
                    {{ $botActive ? 'checked' : '' }}>
                <label for="switch-bot-active" class="switch-label">
                    <div class="switch-track">
                        <div class="switch-thumb"></div>
                    </div>
                </label>
            </div>
            <span class="ml-3 text-white-700 font-medium">
                Bot {{ $botActive ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
        <div class="flex items-center my-4 mx-4 gap-2">
            <div class="switch-container my-4 mx-4 ">
                <input type="checkbox"
                    wire:click="toggleOnlyAdmins"
                    class="switch-input"
                    id="switch-bot-only-admins"
                    {{ $onlyAdmins ? 'checked' : '' }}>
                <label for="switch-bot-only-admins" class="switch-label">
                    <div class="switch-track">
                        <div class="switch-thumb"></div>
                    </div>
                </label>
            </div>
            <span class="ml-3 text-white-700 font-medium">
                Contestar a solo admins
            </span>
        </div>
        <div class="text-sm font-mono m-4">
            <p>🔌 Estado: <span class="font-bold">{{ $connectionStatus }}</span></p>
            @if ($connectionStatus != 'vinculado')
            @if ($lastMessage && isset($lastMessage['data']['qrDataUrl']) && $connectionStatus != 'vinculado')
            <img src="{{ $lastMessage['data']['qrDataUrl'] }}" alt="QR" class="w-64 mx-auto">
            @else
            @if(!empty($qr))
            <img src="{{ $qr }}" alt="QR" class="w-64 mx-auto">
            @else
            <p>Esperar que se muestre QR</p>
            @endif
            @endif
            @else
            <h2>Conectado</h2>
            <!-- <button wire:click="disconnectWhatsapp()" class="disconnect-button">
                Desconectar
            </button> -->
            @endif
        </div>

    </div>
</div>