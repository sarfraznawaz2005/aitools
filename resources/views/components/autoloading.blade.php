<div
    wire:ignore
    x-data="{ loading: true, error: false }"
    x-show="loading || $store.loading || error"
    x-init="$nextTick(() => { loading = false; })"
    class="fixed inset-0 flex items-center justify-center z-[1000]"
    style="display: none;"
>
    <div class="fixed inset-0 bg-black opacity-0 z-[70]"></div>

    <div x-show="!error" class="animate-spin size-12 border-[5px] border-current border-t-transparent text-blue-600 rounded-full" style="position: relative; z-index: 80;">
        <span class="sr-only">Loading...</span>
    </div>

    <div x-show="error" class="text-red-600 font-bold text-xl" style="position: relative; z-index: 80;">
        An error occurred. Please try again.
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('loading', false);
        Alpine.store('error', false);
    });

    if (window.Livewire) {
        initializeLoading();
    } else {
        document.addEventListener('livewire:init', () => {
            initializeLoading();
        });
    }

    function initializeLoading() {
        Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
            Alpine.store('loading', true);
            Alpine.store('error', false);

            succeed(({ status, json }) => {
                Alpine.store('loading', false);
            });

            fail(({ status, body }) => {
                Alpine.store('loading', false);
                Alpine.store('error', true);
                setTimeout(() => {
                    Alpine.store('error', false);
                }, 5000); // Hide error message after 5 seconds
            });
        });

        Livewire.hook('element.updated', (el, component) => {
            Alpine.store('loading', false);
        });

        document.addEventListener('livewire:navigate:start', () => {
            Alpine.store('loading', true);
            Alpine.store('error', false);
        });

        document.addEventListener('livewire:navigate:end', () => {
            Alpine.store('loading', false);
        });
    }
</script>
