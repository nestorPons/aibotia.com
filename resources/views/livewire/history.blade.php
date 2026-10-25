<?php

use App\Services\ApiService;
use function Livewire\Volt\{state, mount};

state([
    'data' => []
]);

mount(function () {
    $this->loadData();
});

$loadData = function () {
    if (!auth()->user()->botname) {
        return;
    }

    $api = new ApiService(auth()->user()->botname, 'messages');
    $data = $api->fetch();

    $this->data = collect($data)
        ->map(function ($items) {
                return [
                    'id'=>$items['id'],
                    'user_name'=>$items['user_name'],
                    'user_message'=>$items['user_message'],
                    'user_name'=>$items['user_name'],
                    'ai_response'=>$items['ai_response'],
                    'prompt_context'=>$items['prompt_context'],
                    'created_at'=>$items['created_at']
                ];
        })
        ->reverse()
        ->toArray();
};

?>

<div class="table-messages-component" wire:key="messages-table">

    <style>
        .table-messages-component table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }

        .table-messages-component th,
        .table-messages-component td {
            border: 1px solid #727272ff;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .table-messages-component th {
            font-weight: bold;
        }

        .table-messages-component tr:nth-child(even) {
        }

        .table-messages-component tr:hover {
        }
    </style>

    <table>
        @if($data && count($data) > 0)
        <thead>
            <tr>
                @foreach(array_keys($data[0]) as $key)
                <th>{{ $key }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $message)
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
            </tr>
            @endforeach
        </tbody>
        @else
        <tr>
            <td colspan="8">No hay mensajes disponibles.</td>
        </tr>
        @endif
    </table>

</div>
