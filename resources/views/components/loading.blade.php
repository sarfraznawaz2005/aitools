<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loading', () => ({
            show: false,
            bgColor: 'bg-yellow-400',
            opacity: 'opacity-75',

            init() {
                Livewire.on('showLoading', (bgColor, opacity) => {
                    this.bgColor = bgColor || this.bgColor;
                    this.opacity = opacity || this.opacity;
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
    <div class="fixed inset-0 bg-transparent opacity-5 z-[70]"></div>

    <div class="relative">
        <span class="flex absolute size-10 right-0">
            <span :class="`animate-ping absolute inline-flex size-full rounded-full ${bgColor} ${opacity}`"></span>
            <!--<span class="relative inline-flex rounded-full size-10 bg-green-500"></span>-->
        </span>
    </div>

    {{--
    <div class="animate-spin size-12 border-[5px] border-current border-t-transparent text-blue-600 rounded-full">
        <span class="sr-only">Loading...</span>
    </div>
    --}}
</div>
