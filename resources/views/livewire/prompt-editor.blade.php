<?php

use function Livewire\Volt\{state, mount, action};

state([
    'prompt' => '',
    'message' => ''
]);

mount(function ($initial = '') {
    $this->prompt = $initial;
});

$save = action(fn()=>$this->message = 'Prompt guardado correctamente.');
?>

<div class="space-y-4 p-4">
    @if ($message)
        <div class="bg-green-100 text-green-800 p-2 rounded">
            {{ $message }}
        </div>
    @endif

    <label for="prompt" class="block font-bold">Editar Prompt:</label>
    <textarea
        id="prompt"
        wire:model.defer="prompt"
        rows="10"
        class="w-full p-2 border rounded"
        placeholder="Escribe tu prompt aquí..."></textarea>

    <button
        wire:click="save"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Guardar
    </button>
</div>
