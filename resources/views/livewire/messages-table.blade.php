<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount};

state([
    'data' => [],
    'filterData' => [],
    'showModal' => false,
    'editIndex' => null,
    'editForm' => [],
    'editJson' => [],
    'arrayKeys' => [],
    'message' => '',
    'status' => '',
]);

mount(function () {
    $this->loadData();
});

$loadData = function () {
    if (!auth()->user()->botname) {
        $this->message = 'Debes crear un bot para poder usar esta funcionalidad';
        $this->status = 'danger';
        return;
    }

    $api = new ApiService(auth()->user()->botname, 'messages');
    $data = $api->fetch();

    $this->data = collect($data)->toArray();
    $this->filterData = collect($this->data)
        ->map(function ($items) {
            unset($items['ai_response'], $items['run_id'], $items['thread_id'], $items['model']);
            return $items;
        })
        ->values()
        ->toArray();
};

$openModal = function (int $index) {
    if (!isset($this->data[$index])) return;

    $this->editIndex = $index;
    $this->editForm = $this->data[$index];
    $this->arrayKeys = [];
    $this->editJson = [];

    foreach ($this->editForm as $key => $value) {
        if (is_array($value)) {
            $this->arrayKeys[] = $key;
            $this->editJson[$key] = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }

    $this->showModal = true;
};

$closeModal = function () {
    $this->showModal = false;
    $this->editIndex = null;
    $this->editForm = [];
    $this->editJson = [];
    $this->arrayKeys = [];
};

$saveRow = function () {
    if ($this->editIndex === null) return;

    foreach ($this->arrayKeys as $key) {
        $json = $this->editJson[$key] ?? '';
        $decoded = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->editForm[$key] = $decoded;
        } else {
            $this->status = 'danger';
            $this->message = "Invalid JSON in field '{$key}'. Please fix it.";
            return;
        }
    }

    try {
        if (isset($this->editForm['id'])) {
            $api = new ApiService(auth()->user()->botname, 'messages');
            $api->update($this->editForm['id'], $this->editForm);
        }
    } catch (\Throwable $e) {
        $this->status = 'danger';
        $this->message = 'Failed to save on server: ' . $e->getMessage();
        return;
    }

    $this->filterData[$this->editIndex] = $this->editForm;

    $this->status = 'success';
    $this->message = 'Row updated successfully.';
    $this->closeModal();
};
?>

<div class="table-messages-component" wire:key="messages-table">

    <table>
        @if($filterData && count($filterData) > 0)
        <thead>
            <tr>
                @foreach(array_keys($filterData[0]) as $key)
                <th>{{ $key }}</th>
                @endforeach
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filterData as $index => $message)
            <tr>
                @foreach($message as $key => $item)
                <td>
                    @if(is_array($item))
                    {{ implode(', ', array_map(fn($v) => is_scalar($v) ? (string)$v : json_encode($v), $item)) }}
                    @else
                    {{ is_bool($item) ? ($item ? 'true' : 'false') : $item }}
                    @endif
                </td>
                @endforeach
                <td>
                    <button class="btn-edit" wire:click="openModal({{ $index }})" type="button" title="Edit row">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M17.414 2.586a2 2 0 00-2.828 0l-9.9 9.9a1 1 0 00-.263.465l-1 4a1 1 0 001.213 1.213l4-1a1 1 0 00.465-.263l9.9-9.9a2 2 0 000-2.828l-1.586-1.586z" />
                            <path fill-rule="evenodd" d="M15 6l-1-1 2-2 1 1-2 2z" clip-rule="evenodd" />
                        </svg>
                    </button>

                </td>
            </tr>
            @endforeach
        </tbody>
        @else
        <tr>
            <td colspan="8">No hay mensajes disponibles.</td>
        </tr>
        @endif
    </table>

    @if($showModal)
    <div class="fixed inset-0 z-[9998] flex items-center justify-center">
        <div class="absolute inset-0 bg-black/70" wire:click="closeModal"></div>

        <div class="relative z-[9999] w-full max-w-3xl rounded-lg shadow-lg p-6 text-gray-100" style="background-color: #333;">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Edit row @if(isset($editForm['id'])) (ID: {{ $editForm['id'] }}) @endif</h2>
                <button type="button" class="text-gray-300 hover:text-white" wire:click="closeModal" aria-label="Close">✕</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[65vh] overflow-auto">
                @foreach($editForm as $key => $value)
                <div class="flex flex-col">
                    <label class="text-sm font-medium mb-1 text-gray-300">{{ $key }}</label>

                    @php $type = gettype($value); @endphp

                    @if(is_bool($value))
                    <input type="checkbox" wire:model.defer="editForm.{{ $key }}" class="h-4 w-4 accent-blue-500">
                    @elseif(is_numeric($value))
                    <input type="number" step="any"
                        wire:model.defer="editForm.{{ $key }}"
                        class="border border-gray-700 rounded p-2 bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0">
                    @elseif(is_array($value))
                    <textarea wire:model.defer="editJson.{{ $key }}" rows="6"
                        class="border border-gray-700 rounded p-2 font-mono text-sm bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"></textarea>
                    <small class="text-gray-400">JSON array/object</small>
                    @elseif(strlen($value) > 50)
                        <textarea wire:model.defer="editForm.{{ $key }}" rows="6"
                        class="border border-gray-700 rounded p-2 font-mono text-sm bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"></textarea>
                    @else
                    <input type="text"
                        wire:model.defer="editForm.{{ $key }}"
                        class="border border-gray-700 rounded p-2 bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0">
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
                <button type="button" class="px-3 py-2 rounded border border-gray-700 text-gray-200 hover:bg-gray-800" wire:click="closeModal">Cancel</button>
                <button type="button" class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" wire:click="saveRow">Save</button>
            </div>
        </div>
    </div>
    @endif

</div>