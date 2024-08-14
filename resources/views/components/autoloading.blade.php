<div
    wire:key="autoloader-{{ uniqid() . microtime(true) }}"
    x-data="{ loading: true }"
    x-show="loading || $store.loading"
    x-init="$nextTick(() => { loading = false; })"
    class="fixed inset-0 flex items-center justify-center z-[1000]"
    style="display: none;"
>
    <div class="fixed inset-0 bg-black opacity-0 z-[70]"></div>

    <div class="animate-spin size-12 border-[5px] border-current border-t-transparent text-blue-600 rounded-full" style="position: relative; z-index: 80;">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('loading', false);

        Alpine.effect(() => {
            console.log('Global loading state:', Alpine.store('loading'));
        });
    });

    if (window.Livewire) {
        console.log('Livewire already initialized');
        initializeLoading();
    } else {
        document.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');
            initializeLoading();
        });
    }

    function initializeLoading() {
        Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
            Alpine.store('loading', true);
            succeed(({ status, json }) => {
                Alpine.store('loading', false);
            });
        });

        Livewire.hook('element.updated', (el, component) => {
            Alpine.store('loading', false);
        });

        document.addEventListener('livewire:navigate:start', () => {
            Alpine.store('loading', true);
        });

        document.addEventListener('livewire:navigate:end', () => {
            Alpine.store('loading', false);
        });
    }
</script>
