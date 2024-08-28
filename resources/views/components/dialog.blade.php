<!-- Dialog Component -->
<div
    x-data="{ open: false }"
    x-on:open-dialog.window="open = true; $nextTick(() => $refs.dialog.showModal())"
    x-on:close-dialog.window="open = false; $refs.dialog.close()"
    {{ $attributes }}
>
    <dialog
        x-ref="dialog"
        @click="$event.target === $refs.dialog && (open = false, $refs.dialog.close())"
        @keydown.escape.window="open = false; $refs.dialog.close()"
        class="rounded-xl shadow-2xl p-0 bg-white dark:bg-gray-800 relative"
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
    >
        <div class="p-5 inline-block max-w-full">
            <div class="text-gray-700 dark:text-gray-300">
                {{ $slot }}
            </div>
        </div>
    </dialog>

    <style>
        dialog {
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
        }
        dialog::backdrop {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        [x-cloak] { display: none !important; }
    </style>
</div>
