<div
    wire:ignore
    x-data="{ loading: true, error: false }"
    x-show="loading || $store.loading || error"
    x-init="$nextTick(() => { loading = false; })"
    class="fixed inset-0 flex items-center justify-center z-[1000]"
    style="display: none;"
>
    <div class="fixed inset-0 bg-black opacity-0 z-[70]"></div>

    <div x-show="!error" class="flex justify-center items-center h-full w-full z-[1000]">
        <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
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
        Livewire.hook('request', ({component, options, payload, respond, succeed, fail}) => {

            // Define the methods that should ignore the loading spinner
            const ignoreMethodsForLoading = ['userMessage', 'loadBots'];

            // loop over the ignoreMethodsForLoading array and skip execution if payload contains any of the methods
            for (let i = 0; i < ignoreMethodsForLoading.length; i++) {
                if (payload.includes(ignoreMethodsForLoading[i])) {
                    return;
                }
            }

            // Otherwise, show the loading spinner
            Alpine.store('loading', true);
            Alpine.store('error', false);

            succeed(({status, json}) => {
                Alpine.store('loading', false);
            });

            fail(({status, body}) => {
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
