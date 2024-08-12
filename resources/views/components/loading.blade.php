<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loading', () => ({
            show: false,

            init() {
                Livewire.on('showLoading', () => {
                    this.show = true;
                });

                Livewire.on('hideLoading', () => {
                    this.show = false;
                });
            }
        }))
    });
</script>

<div
    x-data="loading"
    x-show="show"
    wire:ignore
    x-cloak
    class="fixed inset-0 flex items-center justify-center z-[1000]">
    <div class="fixed inset-0 bg-black opacity-5 z-[70]"></div>
    <div class="animate-spin size-12 border-[5px] border-current border-t-transparent text-blue-600 rounded-full">
        <span class="sr-only">Loading...</span>
    </div>
</div>
