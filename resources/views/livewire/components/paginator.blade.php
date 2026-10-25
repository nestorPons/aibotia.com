<?php
// resources/views/livewire/components/paginator.blade.php

use function Livewire\Volt\{state, mount};

state([
    'currentPage' => 1,
    'itemsPerPage' => 10,
    'totalItems' => 0,
    'totalPages' => 1,
    'perPageOptions' => [5, 10, 25, 50, 100],
    'showPerPageSelector' => true,
    'showPageInfo' => true,
    'previousText' => 'Anterior',
    'nextText' => 'Siguiente',
    'perPageText' => 'Filas por página:',
    'pageText' => 'Página',
    'ofText' => 'de',
    'storageKey' => null,
    'enableStorage' => true
]);

mount(function () {
    if (!$this->storageKey) {
        $this->storageKey = 'paginator_' . $this->getId();
    }
    $this->calculateTotalPages();
});

$calculateTotalPages = function () {
    $this->totalPages = $this->totalItems > 0 ? ceil($this->totalItems / $this->itemsPerPage) : 1;
};

$goToPage = function ($page) {
    if ($page >= 1 && $page <= $this->totalPages) {
        $this->currentPage = $page;
        $this->dispatch('page-changed', page: $this->currentPage);
    }
};

$changePerPage = function ($newPerPage) {
    $newPerPage = (int) $newPerPage;
    $this->itemsPerPage = $newPerPage;
    $this->currentPage = 1;
    $this->calculateTotalPages();
    
    $this->dispatch('per-page-changed', [
        'perPage' => $this->itemsPerPage,
        'page' => $this->currentPage
    ]);
};

$previousPage = function () {
    if ($this->currentPage > 1) {
        $this->goToPage($this->currentPage - 1);
    }
};

$nextPage = function () {
    if ($this->currentPage < $this->totalPages) {
        $this->goToPage($this->currentPage + 1);
    }
};

$updatedTotalItems = function () {
    $this->calculateTotalPages();
    if ($this->currentPage > $this->totalPages) {
        $this->currentPage = max(1, $this->totalPages);
        $this->dispatch('page-changed', page: $this->currentPage);
    }
};

$updatedItemsPerPage = function () {
    $this->calculateTotalPages();
    $this->dispatch('per-page-changed', [
        'perPage' => $this->itemsPerPage,
        'page' => $this->currentPage
    ]);
};
?>

<div class="w-full py-3 flex items-center justify-between space-x-2 gap-2 flex-wrap">
    <div class="flex items-center space-x-2 gap-2">
        {{-- Botón Anterior --}}
        <x-filament::button 
            size="sm" 
            color="gray" 
            wire:click="previousPage"
            :disabled="$currentPage <= 1">
            {{ $previousText }}
        </x-filament::button>

        {{-- Información de página --}}
        @if ($showPageInfo)
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ $pageText }} {{ $currentPage }} {{ $ofText }} {{ $totalPages }}
            </span>
        @endif

        {{-- Botón Siguiente --}}
        <x-filament::button 
            size="sm" 
            color="gray" 
            wire:click="nextPage"
            :disabled="$currentPage >= $totalPages">
            {{ $nextText }}
        </x-filament::button>
    </div>

    {{-- Selector de elementos por página --}}
    @if ($showPerPageSelector)
        <div class="flex items-center space-x-2 gap-2">
            <label for="perPageSelect-{{ $this->getId() }}" class="text-sm text-gray-600 dark:text-gray-400">
                {{ $perPageText }}
            </label>
            
            <div x-data="{
                storageKey: '{{ $storageKey }}',
                enableStorage: {{ $enableStorage ? 'true' : 'false' }},
                
                init() {
                    if (this.enableStorage) {
                        const saved = localStorage.getItem(this.storageKey);
                        if (saved) {
                            const select = this.$refs.select;
                            select.value = saved;
                            // Solo sincronizamos si el valor es diferente para evitar peticiones extra
                            if (parseInt(saved) !== @js($itemsPerPage)) {
                                @this.set('itemsPerPage', parseInt(saved));
                            }
                        }
                    }
                },
                
                handleChange(event) {
                    const value = event.target.value;
                    if (this.enableStorage) {
                        localStorage.setItem(this.storageKey, value);
                    }
                    @this.call('changePerPage', value);
                }
            }">
                <select
                    x-ref="select"
                    id="perPageSelect-{{ $this->getId() }}"
                    value="{{ $itemsPerPage }}"
                    x-on:change="handleChange($event)"
                    class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-2 py-1 w-auto">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" {{ $option == $itemsPerPage ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
</div>