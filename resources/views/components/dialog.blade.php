<div
    x-data="{
        open: false,
        width: '',
        dialogId: '{{ $dialogId }}',
        closeOtherDialogs() {
            document.querySelectorAll('[x-data]').forEach(el => {
                const component = el.__x;
                if (component && component.$data.open && component.$data.dialogId !== this.dialogId) {
                    component.$data.open = false;
                }
            });
        }
    }"
    @open-dialog.window="if($event.detail.id === dialogId) { closeOtherDialogs(); open = true; width = $event.detail.width || 'auto'; }"
    @close-dialog.window="if($event.detail.id === dialogId) open = false"
    x-show="open"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center"
    :id="dialogId"
    {{ $attributes->merge(['wire:key' => 'dialog-'.$dialogId]) }}
>
    <div
        class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"
        @click="open = false"
    ></div>

    <div
        :style="{ width: width }"
        class="relative bg-white rounded-lg shadow-lg p-6 mx-4"
        @click.away="open = false"
    >
        <!-- Close Button -->
        <button
            type="button"
            class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"
            @click="open = false"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Slot for dialog content -->
        <div class="dialog-content">
            {{ $slot }}
        </div>
    </div>
</div>
