{{-- resources/views/components/edit-row-modal.blade.php --}}
@props([
    'show' => false,
    'title' => null,
    'data' => [],
    'close' => 'closeModal',
    'save' => 'save',
])

@if($show)
<div
    class="fixed inset-0 z-[9999] flex items-center justify-center"
    role="dialog"
    aria-modal="true"
    wire:keydown.escape.window="{{ $close }}"
>
 
    <div class="absolute inset-0 bg-black/70" wire:click="{{ $close }}"></div>

    <div class="relative z-[9999] w-full max-w-3xl rounded-lg shadow-lg p-6 text-gray-100" style="background-color:#333;">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">{{ $title }}</h2>
            <button
                type="button"
                class="text-gray-300 hover:text-white"
                wire:click="{{ $close }}"
                aria-label="Close"
            >✕</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[65vh] overflow-auto">
            @foreach($data as $key => $value)
                <div class="flex flex-col">
                    <label class="text-sm font-medium mb-1 text-gray-300">{{ $key }}</label>

                    @if(is_bool($value))
                        <input 
                            type="checkbox"   
                            id="{{$key}}"                          
                            value="{{$value}}"
                            class="h-4 w-4 accent-blue-500">
                    @elseif(is_numeric($value))
                        <input
                            id="{{$key}}"                          
                            type="number"
                            step="any"
                            value="{{$value}}"

                            class="border border-gray-700 rounded p-2 bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"

                        >
                    @elseif(is_array($value))
                        <textarea
                            id="{{$key}}"                          
                            rows="6"
                            class="border border-gray-700 rounded p-2 font-mono text-sm bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"
                            >{{ json_encode($value) }}</textarea>
                        <small class="text-gray-400">JSON array/object</small>
                    @elseif(strlen($value) > 50)
                        <textarea
                            id="{{$key}}"                          
                            rows="6"
                            class="border border-gray-700 rounded p-2 font-mono text-sm bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"
                            >
                            {{ $value }}
                        </textarea>
                        <small class="text-gray-400">JSON array/object</small>
                    @else
                        <input
                            id="{{$key}}"                          
                            type="text"
                            class="border border-gray-700 rounded p-2 bg-transparent text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-0"
                            value="{{$value}}"
                        >
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex items-center justify-end gap-2">
            <button
                type="button"
                class="px-3 py-2 rounded border border-gray-700 text-gray-200 hover:bg-gray-800"
                wire:click="{{ $close }}"
            >Cancel</button>

            <button
                type="button"
                class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                wire:click="{{ $save }}"
            >Save</button>
        </div>
    </div>
</div>
@endif
