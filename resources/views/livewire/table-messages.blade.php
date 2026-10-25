<div class="table-messages-component">
    <style>
        .table-messages-component table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border: 1px solid #d1d5db;
        }

        .table-messages-component thead {
            width: 100%;
            background-color: #f9fafb;
            /* gray-50 */
        }

        .dark .table-messages-component thead {
            background-color: #1f2937;
            /* gray-800 */
        }

        .table-messages-component th,
        .table-messages-component td {
            color: #111827;
            font-size: 14px;
            width: 14.28%;
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .table-messages-component th,
        .dark .table-messages-component td {
            border-bottom: 1px solid #374151;
            /* gray-700 */
            color: #d1d5db;
            /* gray-300 */
        }

        .table-messages-component th {
            text-transform: uppercase;
            font-size: 12px;
            color: #6b7280;
            /* gray-500 */
        }

        .dark .table-messages-component th {
            color: #9ca3af;
            /* gray-400 */
        }


        .table-messages-component tr:hover {
            background-color: #f3f4f6;
            /* gray-100 */
        }
        .table-messages-component tr:hover {
            background-color: #9ca3af;
            /* gray-700 */
        }
        .dark .table-messages-component tr:hover {
            background-color: #374151;
            /* gray-700 */
        }

        .table-messages-component .actions-header {
            width: 24.28%;
        }

        .table-messages-component .btn {
            padding: 6px 12px;
            font-size: 0.875rem;
            /* text-sm */
            font-weight: 500;
            border-radius: 0.5rem;
            /* rounded-lg */
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .button-green {
            background-color: #22c55e;
            /* green-500 */
            color: white;
        }

        .button-green:hover {
            background-color: #16a34a;
            /* green-600 */
        }

        .button-red {
            background-color: #ef4444;
            /* red-500 */
            color: white;
        }

        .button-red:hover {
            background-color: #dc2626;
            /* red-600 */
        }
    </style>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th style="width: 235px;">Código</th>
                <th style="width: 75px;">Menú</th>
                <th>Orden</th>
                <th>Mensaje</th>
                <th></th>
                <th>Acciones</th>
                <th>Adjuntos</th>
                <th class="actions-header"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($messages as $message)
            <tr>
                @foreach($message as $cell)
                <td>
                    @if (is_array($cell) && count($cell) > 0)
                    {{ implode(', ', $cell) }}
                    @elseif (is_array($cell) && count($cell) === 0)
                    <!-- No imprimir nada si el array está vacío -->
                    @else
                    {{ $cell }}
                    @endif
                </td>
                @endforeach
                <td>
                    <button class="btn button-green ">Editar</button>
                    <button class="btn button-red ">Eliminar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>