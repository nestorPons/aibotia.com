<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount, on, updated, updating};

state([
    'showDeleteModal' => false,
    'showForceModal' => false,
    'showEditModal' => false,
    'showCreateModal' => false,
    'data' => [],
    'user' => [],
    'editIndex' => null,
    'currentBlock' => [
        'id' => null,
        'code' => '',
        'description' => '',
        'translations' => [],
    ],
    'message' => '',
    'status' => '',
    'page' => 1,
    'perPage' => 10,
    'deleteCode' => null,
    'fillable' => [],
    'type' => [],
    'deleteIndex' => null,
    'forceIndex' => null,
    'showOnlyActive' => false,
    'search' => '',
    'orderBy' => 'last_message_date',
    'orderDir' => 'desc',
    'totalItems' => 0,
    'showDetailsModal' => false,
    'detailsIndex' => null,
]);

mount(function () {
    $this->loadData();
    $this->fillable = ['alias', 'active', 'thread_id', 'role'];
    $this->type['active'] = 'boolean';
});

$searchUsers = function () {
    $this->page = 1;
    $this->loadData();
};

updating(['search' => function () {
    $this->page = 1;
    $this->loadData();
}]);

$loadData = function () {
    if (!auth()->user()?->botname) {
        $this->message = 'Debes crear un bot para poder usar esta funcionalidad';
        $this->status = 'danger';
        return;
    }

    $api = new ApiService(auth()->user()->botname, 'users');
    $response = $api->fetch([
        'page' => $this->page,
        'perPage' => $this->perPage,
        'search' => $this->search,
        'orderBy' => $this->orderBy,
        'orderDir' => $this->orderDir,
    ]);

    if (!$response || isset($response['error'])) {
        $this->message = 'Error al cargar los usuarios. Asegúrate de que tu bot está activo.';
        $this->status = 'danger';
        return;
    }

    $this->data = collect($response['data'])
        ->map(function ($item) {
            unset($item['created_at'], $item['updated_at'], $item['thread_id'], $item['topic']);
            $item['active'] = (bool) $item['active'];

            if (isset($item['temperature'])) {
                $item['temperature'] = round($item['temperature'], 2);
            }

            if (!empty($item['last_message_date'])) {
                $item['last_message_date'] = \Carbon\Carbon::parse($item['last_message_date'])->format('Y-m-d H:i:s');
            }

            return $item;
        })
        ->values()
        ->toArray();

    $this->totalItems = $response['meta']['total'];
    $this->fillable = ['alias', 'active', 'thread_id'];
    $this->type['active'] = 'boolean';
    $this->type['role'] = [0 => 'Super', 1 => 'Admin', 2 => 'Usuario'];
};

// 🔹 FIX: Added $this->loadData() call
$sortByColumn = function ($column) {
    if ($this->orderBy === $column) {
        $this->orderDir = $this->orderDir === 'asc' ? 'desc' : 'asc';
    } else {
        $this->orderBy = $column;
        $this->orderDir = 'asc';
    }
    $this->loadData(); // ⭐ THIS WAS MISSING!
};

$updateField = function ($index) {
    $userId = $this->data[$index]['id'];
    $useIa = !empty($this->data[$index]['active']) ? 1 : 0;

    $api = new ApiService(auth()->user()->botname, 'users');
    $response = $api->update($userId, [
        'alias' => $this->data[$index]['alias'],
        'active' => $useIa
    ]);

    if ($response->successful()) {
        $this->status = 'success';
    } else {
        $this->message = 'Error: ' . $response->body();
        $this->status = 'error';
    }

    $this->data[$index]['active'] = $useIa;
};

$edit = fn($index) => ($this->editIndex = $index);

$confirmDelete = function ($index) {
    $this->deleteIndex = $index;
    $this->showDeleteModal = true;
};

$confirmForceReply = function ($index) {
    $this->forceIndex = $index;
    $this->showForceModal = true;
};

$delete = function () {
    $index = $this->deleteIndex;
    $api = new ApiService(auth()->user()->botname, 'users');
    $id = $this->data[$index]['id'];
    $response = $api->delete($id);
    if ($response) {
        $this->data = array_values(collect($this->data)->except($index)->toArray());
        \Filament\Notifications\Notification::make()->title('Usuario eliminado')->success()->send();
    } else {
        \Filament\Notifications\Notification::make()->title('Error al eliminar el usuario')->danger()->send();
    }
    $this->showDeleteModal = false;
};


updated([
    'perPage' => function () {
        $this->page = 1;
        $this->loadData();
    },
]);

$clearSearch = function () {
    $this->search = '';
    $this->page = 1;
    $this->loadData();
};

$forceReply = function () {
    $index = $this->forceIndex;
    $userId = $this->data[$index]['id'];

    $api = new ApiService(auth()->user()->botname, 'users');
    $response = $api->forceReply($userId);

    $this->showForceModal = false;

    if ($response->successful()) {
        \Filament\Notifications\Notification::make()->title('Mensaje de fuerza enviado al usuario')->success()->send();
    } else {
        \Filament\Notifications\Notification::make()->title('Error al enviar el mensaje de fuerza')->danger()->send();
    }
};

$showEditUser = function ($index) {
    $user = $this->data[$index] ?? [];
    $this->user = [
        'id' => $user['id'] ?? null,
        'name' => $user['name'] ?? ($user['full_name'] ?? ''),
        'alias' => $user['alias'] ?? '',
        'lang' => $user['lang'] ?? '',
        'buffer_time' => $user['buffer_time'] ?? '',
        'last_message' => $user['last_message_date'] ?? '',
        'labels' => $user['labels'] ?? '',
        'temperature' => $user['temperature'] ?? '',
        'status' => $user['status'] ?? '',
        'role' => $user['role'] ?? '',
        'active' => (bool)($user['active'] ?? false),
    ];

    $this->showEditModal = true;
};

$saveUser = function ($payload = null) {
    $editForm = $payload['editForm'] ?? $this->user;

    $api = new ApiService(auth()->user()->botname, 'users');
    $response = $api->update($editForm['id'], $editForm);

    if ($response->successful()) {
        $this->loadData();
        \Filament\Notifications\Notification::make()
            ->title('Usuario actualizado correctamente')
            ->success()
            ->send();
    } else {
        \Filament\Notifications\Notification::make()
            ->title('Error al actualizar el usuario')
            ->danger()
            ->send();
    }

    $this->showEditModal = false;
};

on([
    'per-page-changed' => function ($data) {
        $this->perPage = $data['perPage'];
        $this->page = $data['page'];
        $this->loadData(); // Reload when pagination changes
    },
    'page-changed' => function ($page) {
        $this->page = $page;
        $this->loadData(); // Reload when page changes
    },
    'modalSaved' => function ($data) {
        $this->saveUser($data);
    },
    'modalClosed' => function () {
        $this->showEditModal = false;
    },
]);


?>


<div class="table-messages-component"
    x-data="{ init() { // Recupera el valor guardado de 'showOnlyActive' const showActive = localStorage.getItem('showOnlyActive'); if (showActive !== null && (showActive === 'true') !== @js($showOnlyActive)) { $wire.set('showOnlyActive', showActive === 'true'); } // Recupera el valor guardado de 'perPage' const saved = localStorage.getItem('users_table_per_page'); if (saved && parseInt(saved) !== @js($perPage)) { $wire.set('perPage', parseInt(saved)); } // Recupera el valor guardado de 'search' const savedSearch = localStorage.getItem('userSearch'); if (savedSearch && savedSearch !== @js($search)) { $wire.set('search', savedSearch); } } }"
    x-init="init()">
    
    <x-confirm-modal :show="$showDeleteModal" title="¿Confirmar eliminación?"
        message="¿Estás seguro de que quieres eliminar este usuario?" cancel="$set('showDeleteModal', false)"
        confirm="delete" />
    <x-confirm-modal :show="$showForceModal" title="Confirmar enviar respuesta"
        message="¿Estás seguro de que quieres forzar la respuesta de este usuario?"
        cancel="$set('showForceModal', false)" confirm="forceReply" />
    @if($message)
        <div class="alert alert-{{ $status }} mb-4 p-2"> {{ $message }} </div>
    @endif<!-- Barra de búsqueda -->
    <div class="mb-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
        <div class="relative flex-1 max-w-md"> <input type="text" wire:model="search" wire:keydown.enter="searchUsers"
                placeholder="Buscar usuarios por nombre, alias, ID..."
                class="block w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
                @if($search)
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center"> <button wire:click="clearSearch"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" title="Limpiar búsqueda">
                    </button> </div>
            @endif
        </div>
        @if($search)
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Resultados para:
                <span class="font-medium">"{{ $search }}"</span>
                <span class="text-xs">
                    ({{ $totalItems }} encontrados)
                </span>
            </div>
        @endif
    </div>
    <table class="w-full table-auto border-separate border-spacing-y-1 text-sm text-gray-800 dark:text-gray-200">
        <thead class="bg-gray-100 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-300 uppercase font-semibold">
            <tr>
                <th class="px-4 py-2 cursor-pointer" wire:click="sortByColumn('id')">
                    ID
                    @if($orderBy === 'id')
                        <span>{{ $orderDir === 'asc' ? '▲' : '▼' }}</span>
                    @endif
                </th>
                <th class="px-4 py-2 cursor-pointer" wire:click="sortByColumn('name')">
                    Nombre
                    @if($orderBy === 'name')
                        <span>{{ $orderDir === 'asc' ? '▲' : '▼' }}</span>
                    @endif
                </th>
                <th class="px-4 py-2 cursor-pointer" wire:click="sortByColumn('alias')">
                    Alias
                    @if($orderBy === 'alias')
                        <span>{{ $orderDir === 'asc' ? '▲' : '▼' }}</span>
                    @endif
                </th>
                <th class="px-4 py-2">Activo</th>
                <th class="px-4 py-2 cursor-pointer" wire:click="sortByColumn('last_message_date')">
                    Último mensaje
                    @if($orderBy === 'last_message_date')
                        <span>{{ $orderDir === 'asc' ? '▲' : '▼' }}</span>
                    @endif
                </th>
                <th class="px-4 py-2">Etiquetas</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $index => $row)
                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded">
                    <td class="px-4 py-2">{{ $row['id'] }}</td>
                    <td class="px-4 py-2">
                        {{ $row['name'] ?? '—' }}</td>
                    <td class="px-4 py-2">
                        {{ $row['alias'] ?? '—' }}</td>
                    <td class="px-4 py-2">
                    <label class="switch">
                        <input 
                            type="checkbox"
                            wire:change="updateField({{ $index }})"
                            wire:model="data.{{ $index }}.active"
                        >
                        <span class="slider"></span>
                    </label>
                    </td>
                    <td class="px-4 py-2">
                        {{ $row['last_message_date'] ?? '—' }}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex gap-2 justify-center">
                            <x-filament::button color="gray" wire:click="showEditUser({{ $index }})">
                                <x-heroicon-s-pencil class="w-5 h-5 mr-1" />
                            </x-filament::button>
                            <x-filament::button color="info" tag="a"
                                href="{{ route('users.conversation', ['id' => $row['id']]) }}">
                                <x-heroicon-s-chat-bubble-left-ellipsis class="w-5 h-5 mr-1" />
                            </x-filament::button>
                            <x-filament::button color="primary" wire:click="confirmForceReply({{ $index }})">
                                <x-heroicon-s-paper-airplane class="w-5 h-5 mr-1" />
                            </x-filament::button>
                            <x-filament::button color="danger" wire:click="confirmDelete({{ $index }})">
                                <x-heroicon-s-trash class="w-5 h-5 mr-1" />
                            </x-filament::button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No hay usuarios registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <livewire:components.paginator :current-page="$page" :items-per-page="$perPage" :total-items="$totalItems"
        :enable-storage="true" storage-key="users_table_per_page" wire:key="users-paginator" />

    <!-- Modales -->
    @if($showEditModal)
        <div x-data="{ show: @entangle('showEditModal') }" x-show="show" x-transition.opacity.duration.200ms x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" 
            @click.self="show = false"
                >
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl p-6 relative"
                    x-transition.scale.origin.center.duration.200ms>
                    {{-- Cabecera --}}
                    <div
                        class="flex items-center justify-between mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Editar usuario #{{ $user['id'] ?? '' }}
                        </h2>
                        <button type="button" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" @click="show = false">✕</button>
                    </div>

                    {{-- Formulario --}}
     <form wire:submit.prevent="saveUser" class="space-y-3 max-h-[70vh] overflow-y-auto pr-2">
    {{-- Cada fila: label + campo alineados --}}
    <div class="flex items-center justify-between gap-3">
        <label style="width: 30%;" class= "text-sm font-medium text-gray-200 dark:text-gray-300">Nombre</label>
        <input type="text" wire:model.defer="user.name"
            class="flex-1  p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label style="width: 30%;" class=" text-sm font-medium text-gray-200 dark:text-gray-300">Alias</label>
        <input type="text" wire:model.defer="user.alias"
            class="flex-1  p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Idioma</label>
        <select wire:model.defer="user.lang"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">—</option>
            <option value="es">Español</option>
            <option value="en">Inglés</option>
            <option value="pt">Portugués</option>
        </select>
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Satisfacción</label>
        <input type="number" min="0" max="10" wire:model.defer="user.temperature"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Tiempo de respuesta</label>
        <input type="text" wire:model.defer="user.buffer_time"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Rol</label>
        <select wire:model.defer="user.role"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="0">Super</option>
            <option value="1">Admin</option>
            <option value="2">Usuario</option>
        </select>
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Último mensaje</label>
        <input type="text" wire:model.defer="user.last_message"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Etiquetas</label>
        <input type="text" wire:model.defer="user.labels"
            class="flex-1 p-2 border rounded bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    <div class="flex items-center justify-between gap-3">
        <label class="w-30 text-sm font-medium text-gray-200 dark:text-gray-300">Activo</label>
        <input type="checkbox" wire:model.defer="user.active"
            class="h-4 w-4 accent-indigo-600 dark:accent-indigo-500">
    </div>

    {{-- Botones inferiores --}}
    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 bg-white dark:bg-gray-800 mt-4">
        <button type="button" wire:click="$set('showEditModal', false)"
            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
            Cancelar
        </button>

        <button type="submit"
            class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white">
            Guardar
        </button>
    </div>
</form>

                </div>
        </div>
    @endif
<style>
.switch {
    position: relative;
    display: inline-block;
    width: 46px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #d1d5db;
    transition: .3s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

.switch input:checked + .slider {
    background-color: #4f46e5;
}

.switch input:checked + .slider:before {
    transform: translateX(22px);
}

.dark .slider {
    background-color: #374151;
}

.dark .switch input:checked + .slider {
    background-color: #6366f1;
}
</style>

</div>