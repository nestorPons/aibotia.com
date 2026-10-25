<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount, on};

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
on(['page-changed' => function ($page) {
    $this->page = $page;
    $this->applyPagination();
}]);

on(['per-page-changed' => function ($data) {
    $this->perPage = $data['perPage'];
    $this->page = $data['page'];
    $this->applyPagination();
}]);
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

    $response = $this->currentBlock['id'] ?
        // Actualizar
         $api->update($code, $payload):
        // Crear
         $api->create($payload);

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

$updatingPage = function () {
    $this->applyPagination();
};

$updatedPerPage = function () {
    $this->page = 1; // Reinicia a la primera página
    $this->applyPagination();
};
?>

<div class="table-messages-component">
    <x-confirm-modal
        :show="$showDeleteModal"
        title="¿Confirmar eliminación?"
        message="¿Estás seguro de que quieres eliminar este bloque?"
        cancel="$set('closeEditModal', false)"
        confirm="deleteBlock" />

    {{-- Modal Editar Bloque --}}
    <x-custom-modal
        :show="$showEditModal"
        :data="$currentBlock"
        title="Editar Bloque"
        width="2xl"
        close="$set('showEditModal', false)">

    </x-custom-modal>

    {{-- Modal Crear Bloque --}}
    <x-filament::modal
        :show="$showCreateModal"
        title="Nuevo Bloque"
        width="2xl"
        close="$set('showCreateModal', false)">
    
    </x-filament::modal>

    @if ($message)
    <div class="alert alert-{{ $status }} mb-4 p-2">
        {{ $message }}
    </div>
    @endif

    <x-filament::button color="primary" wire:click="openCreateModal" class="mb-4" style="z-index: -1">
        + Nuevo Bloque
    </x-filament::button>

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
                <td class="px-4 py-2 flex space-x-4 gap-2">
                    <x-filament::button color="primary" wire:click="openEditModal({{ $index }})">
                        <x-heroicon-s-pencil-square class="w-5 h-5 mr-1" />
                    </x-filament::button>
    
                    <x-filament::button color="danger" wire:click="confirmDelete({{ $index }})">
                        <x-heroicon-s-trash class="w-5 h-5 mr-1" />
                    </x-filament::button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <livewire:components.paginator
        :current-page="$page"
        :items-per-page="$perPage"
        :total-items="count($allData)"
        :enable-storage="true"
        storage-key="instructions_table"
        wire:key="instructions-paginator" />
</div>