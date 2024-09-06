<div
    x-data="{
        open: false,
        closeOtherDialogs() {
            document.querySelectorAll('dialog').forEach(el => {
                if (el.open && el !== $refs.dialog) {
                    el.close();
                }
            });
        },
        openDialog() {
            this.closeOtherDialogs();
            $refs.dialog.showModal();
            this.open = true;
        },
        closeDialog() {
            $refs.dialog.close();
            this.open = false;
        }
    }"
    class="inline"
>
    <!-- Trigger Button -->
    <button @click="openDialog" {{ $attributes->merge(['class' => '']) }}>
        {{ $slot }}
    </button>

    <!-- Confirmation Dialog -->
    <dialog
        x-ref="dialog"
        @close="open = false"
        class="rounded-lg p-6 shadow-xl backdrop:bg-black backdrop:bg-opacity-30"
    >
        <div class="dialog-content">
            <div class="mb-4 font-semibold text-lg text-gray-500">{{$text ?? 'Are you sure you want to delete?'}}</div>

            <div class="flex justify-end gap-4">
                <button @click="closeDialog" class="py-2 px-4 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button
                    wire:click="{{ $call }}"
                    @click="closeDialog"
                    class="py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700"
                >
                    <x-icons.ok class="size-4 inline-block"/>
                    Delete
                </button>
            </div>
        </div>
    </dialog>
</div>
