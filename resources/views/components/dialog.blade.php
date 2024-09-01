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
    wire:ignore.self
    @open-dialog.window="if($event.detail.id === dialogId) { closeOtherDialogs(); open = true; width = $event.detail.width || 'auto'; }"
    @close-dialog.window="if($event.detail.id === dialogId) open = false"
    x-show="open"
    x-transition:enter="ease-out duration-200"
    x-transition:leave="ease-in duration-100"
    x-cloak
    class="fixed inset-0 z-[100] flex justify-center"
    :id="dialogId"
    {{ $attributes->merge(['wire:key' => 'dialog-'.$dialogId]) }}
>
    <div
        class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-[2px]"
        @click="open = false"
    ></div>

    <div
        :style="{ width: width, top: '15%' }"
        class="absolute bg-white rounded-lg shadow-lg p-4 mx-4"
        @click.away="open = false"
    >

        <div class="dialog-content">
            {{ $slot }}
        </div>
    </div>
</div>
