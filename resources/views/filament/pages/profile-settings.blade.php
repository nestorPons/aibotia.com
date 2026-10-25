<x-filament::page>
    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-4">
        Guardar cambios
    </x-filament::button>
</x-filament::page>
