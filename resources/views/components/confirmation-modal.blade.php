@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <style>
        .modal-content {
            background-color: #ffffff;
            padding: 1.25rem 1rem 1rem;
            border-radius: 0.5rem;
        }

        .modal-content.dark {
            background-color: #1f2937;
            /* gray-800 */
        }

        .modal-icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 3rem;
            height: 3rem;
            margin-left: auto;
            margin-right: auto;
            border-radius: 9999px;
            background-color: #fee2e2;
            /* red-100 */
            flex-shrink: 0;
        }

        .modal-icon-container.dark .modal-icon {
            color: #f87171;
            /* red-400 */
        }

        .modal-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #dc2626;
            /* red-600 */
        }

        .modal-body {
            margin-top: 0.75rem;
            text-align: center;
        }

        @media (min-width: 640px) {
            .modal-body {
                margin-top: 0;
                margin-left: 1rem;
                text-align: left;
            }
        }

        .modal-title {
            font-size: 1.125rem;
            font-weight: 500;
            color: #111827;
            /* gray-900 */
        }

        .modal-title.dark {
            color: #f3f4f6;
            /* gray-100 */
        }

        .modal-text {
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            /* gray-600 */
        }

        .modal-text.dark {
            color: #9ca3af;
            /* gray-400 */
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            background-color: #f3f4f6;
            /* gray-100 */
        }

        .modal-footer.dark {
            background-color: #1f2937;
            /* gray-800 */
        }
    </style>
    <div class="modal-content dark">
        <div class="sm:flex sm:items-start">
            <div class="modal-icon-container dark">
                <svg class="modal-icon" ...></svg>
            </div>
            <div class="modal-body">
                <h3 class="modal-title dark">{{ $title }}</h3>
                <div class="modal-text dark">{{ $content }}</div>
            </div>
        </div>
    </div>
    <div class="modal-footer dark">
        {{ $footer }}
    </div>

</x-modal>