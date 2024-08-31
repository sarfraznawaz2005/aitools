<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('expiredNotification', () => ({
            expired: false,

            init() {
                Livewire.hook('request', ({fail}) => {
                    fail(({status, preventDefault}) => {
                        if (status === 419) {
                            this.expired = true;
                            preventDefault();
                        }
                    })
                })
            }
        }))
    });
</script>
<div
    wire:ignore
    x-data="expiredNotification"
>
    <div style="display: none;" x-show="expired"
         class="fixed z-[1000] bottom-0 inset-x-0 px-4 pb-4 inset-0 flex items-center justify-center">

        <div x-show="expired">
            <div class="fixed inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div x-show="expired"
             x-on:click.outside="expired = false"
             x-on:keydown.window.escape="expired = false"
             class="relative bg-gray-100 rounded-lg px-4 pt-5 pb-4 overflow-hidden shadow-xl">

            <div x-show="expired" class="w-full text-center">
                <p class="font-bold text-red-600 mb-4">This Page has Expired</p>
                <p class="mb-4">Click the button below to refresh the page.</p>

                <div class="flex justify-center">
                    <x-gradient-button x-on:click="window.location.reload()">
                        <x-icons.refresh/> Refresh Page
                    </x-gradient-button>
                </div>
            </div>

        </div>

    </div>

</div>


