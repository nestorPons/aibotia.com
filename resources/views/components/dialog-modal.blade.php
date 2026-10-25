<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount};

state([
    'showDeleteModal' => false,
    'showEditModal' => false,
    'showCreateModal' => false,
    'data' => [],
    'allData' => [],
    'editIndex' => null,
    'currentBlock' => [
        'id' => null,
        'code' => '',
        'description' => '',
        'translations' => []
    ],
    'message' => "",
    'status' => "",
    'page' => 1,
    'perPage' => 10,
    'deleteCode' => null
]);

mount(function () {
    $this->loadData();
});

$loadData = function () {
    $api = new ApiService(auth()->user()->botname, 'instructions');
    $response = $api->fetch();

    if (is_array($response)) {
        $this->allData = $response;
        $this->applyPagination();
    } else {
        $this->message = 'Error al cargar datos de la API';
        $this->status = 'danger';
    }
};

$applyPagination = function () {
    $items = collect($this->allData);
    $this->data = $items
        ->slice(($this->page - 1) * $this->perPage, $this->perPage)
        ->values()
        ->toArray();
};

$openEditModal = function ($index) {
    $this->editIndex = $index;
    $this->currentBlock = $this->data[$index];
    $this->showEditModal = true;
};

$openCreateModal = function () {
    $this->currentBlock = [
        'id' => null,
        'code' => '',
        'description' => '',
        'translations' => []
    ];
    $this->showCreateModal = true;
};

$saveBlock = function () {
    $api = new ApiService(auth()->user()->botname, 'instructions');
    $code = $this->currentBlock['code'];
    $payload = [
        'block' => [
            'description' => $this->currentBlock['description']
        ],
        'translations' => $this->currentBlock['translations']
    ];

    if ($this->currentBlock['id']) {
        $response = $api->update($code, $payload);
    } else {
        $response = $api->create($payload);
    }

    if ($response->successful()) {
        $this->loadData();
        $this->showEditModal = false;
        $this->showCreateModal = false;
        $this->message = 'Bloque guardado correctamente';
        $this->status = 'success';
    } else {
        $this->message = 'Error al guardar: ' . $response->body();
        $this->status = 'error';
    }
};

$confirmDelete = function ($index) {
    $this->deleteCode = $this->data[$index]['code'];
    $this->showDeleteModal = true;
};

$deleteBlock = function () {
    $api = new ApiService(auth()->user()->botname, 'instructions');
    $response = $api->delete($this->deleteCode, $this->data);

    if ($response->successful()) {
        $this->loadData();
        \Filament\Notifications\Notification::make()
            ->title('Bloque eliminado')
            ->success()
            ->send();
    } else {
        $this->message = 'Error al eliminar: ' . $response->body();
        $this->status = 'error';
    }

    $this->showDeleteModal = false;
};

$updatedPerPage = function () {
    $this->page = 1;
    $this->applyPagination();
};
?>

<div class="table-messages-component">
    <x-confirm-modal
        :show="$showDeleteModal"
        title="¿Confirmar eliminación?"
        message="¿Estás seguro de que quieres eliminar este bloque?"
        cancel="$set('showDeleteModal', false)"
        confirm="deleteBlock"
    />

    {{-- Modal Editar Bloque --}}
    <x-dialog-modal wire:model="showEditModal" maxWidth="2xl">
        <x-slot name="title">
            Editar Bloque
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveBlock" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                    <input type="text"
                        wire:model.defer="currentBlock.code"
                        class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white"
                        disabled />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <input type="text"
                        wire:model.defer="currentBlock.description"
                        class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white" />
                </div>

                <h3 class="mt-4 mb-2 font-bold">Traducciones</h3>
                @foreach ($currentBlock['translations'] as $tIndex => $translation)
                    <div class="mb-3 border p-2 rounded">
                        <label class="block text-sm font-medium">Idioma</label>
                        <input type="text"
                            wire:model.defer="currentBlock.translations.{{ $tIndex }}.language"
                            class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white" />

                        <label class="block text-sm font-medium mt-2">Contenido</label>
                        <textarea
                            wire:model.defer="currentBlock.translations.{{ $tIndex }}.content"
                            rows="3"
                            class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white"
                        ></textarea>

                        <button type="button"
                            wire:click="$pull('currentBlock.translations', {{ $tIndex }})"
                            class="mt-2 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            🗑 Quitar Traducción
                        </button>
                    </div>
                @endforeach

                <button type="button"
                    wire:click="$push('currentBlock.translations', { language: '', content: '' })"
                    class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    + Añadir traducción
                </button>
            </form>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="saveBlock"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Guardar
            </button>
            <button wire:click="$set('showEditModal', false)"
                class="ml-2 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                Cancelar
            </button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal Crear Bloque --}}
    <x-dialog-modal wire:model="showCreateModal" maxWidth="2xl">
        <x-slot name="title">
            Nuevo Bloque
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="saveBlock" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código</label>
                    <input type="text"
                        wire:model.defer="currentBlock.code"
                        class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                    <input type="text"
                        wire:model.defer="currentBlock.description"
                        class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white" />
                </div>

                <h3 class="mt-4 mb-2 font-bold">Traducciones</h3>
                @foreach ($currentBlock['translations'] as $tIndex => $translation)
                    <div class="mb-3 border p-2 rounded">
                        <label class="block text-sm font-medium">Idioma</label>
                        <input type="text"
                            wire:model.defer="currentBlock.translations.{{ $tIndex }}.language"
                            class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white" />

                        <label class="block text-sm font-medium mt-2">Contenido</label>
                        <textarea
                            wire:model.defer="currentBlock.translations.{{ $tIndex }}.content"
                            rows="3"
                            class="w-full p-2 border rounded bg-gray-50 dark:bg-gray-800 dark:text-white"
                        ></textarea>

                        <button type="button"
                            wire:click="$pull('currentBlock.translations', {{ $tIndex }})"
                            class="mt-2 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            🗑 Quitar Traducción
                        </button>
                    </div>
                @endforeach

                <button type="button"
                    wire:click="$push('currentBlock.translations', { language: '', content: '' })"
                    class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    + Añadir traducción
                </button>
            </form>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="saveBlock"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Crear
            </button>
            <button wire:click="$set('showCreateModal', false)"
                class="ml-2 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                Cancelar
            </button>
        </x-slot>
    </x-dialog-modal>

    @if ($message)
        <div class="alert alert-{{ $status }} mb-4 p-2">
            {{ $message }}
        </div>
    @endif

    <button wire:click="openCreateModal"
        class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
        + Nuevo Bloque
    </button>

    <table class="w-full table-auto border-separate border-spacing-y-1 text-sm text-gray-800 dark:text-gray-200">
        <thead class="bg-gray-100 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-300 uppercase font-semibold">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Código</th>
                <th class="px-4 py-2">Descripción</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $row)
            <tr class="bg-white dark:bg-gray-900 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded">
                <td class="px-4 py-2">{{ $row['id'] }}</td>
                <td class="px-4 py-2">{{ $row['code'] }}</td>
                <td class="px-4 py-2">{{ Str::limit($row['description'], 50) }}</td>
                <td class="px-4 py-2 flex space-x-2">
                    <button wire:click="openEditModal({{ $index }})"
                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Editar
                    </button>
                    <button wire:click="confirmDelete({{ $index }})"
                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                        Eliminar
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 flex items-center space-x-2">
        @php
            $totalPages = ceil(count($allData) / $perPage);
        @endphp

        @if ($page > 1)
            <button wire:click="$set('page', {{ $page - 1 }})"
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Anterior</button>
        @endif

        <span>Página {{ $page }} de {{ $totalPages }}</span>

        @if ($page < $totalPages)
            <button wire:click="$set('page', {{ $page + 1 }})"
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Siguiente</button>
        @endif

        <label for="perPageSelect">Filas por página:</label>
        <select id="perPageSelect"
            class="ml-2 p-1 border rounded"
            wire:model="perPage"
            wire:change="updatedPerPage">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>
</div>
