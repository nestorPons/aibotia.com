@props([
    'show' => false,
    'title' => '',
    'message' => '',
    'confirm' => '',
    'cancel' => '',
    'confirmLabel' => 'Confirmar',
    'cancelLabel' => 'Cancelar',
])
@if ($show)
<div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl w-[90%] max-w-md text-gray-800 dark:text-gray-100">
        <h2 class="text-lg font-semibold mb-4">{{ $title }}</h2>
        <p class="mb-6">{{ $message }}</p>
        <div class="flex justify-end space-x-2">
            <button wire:click="{{ $confirm }}" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition">
                {{ $confirmLabel }}
            </button>
            <button wire:click="{{ $cancel }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition">
                {{ $cancelLabel }}
            </button>
        </div>
    </div>
</div>
@endif
