<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loading', () => ({
            show: false,

            init() {
                Livewire.on('showLoading', () => this.show = true);
                Livewire.on('hideLoading', () => this.show = false);
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

    <span
        class="mb-4 animate-ping inline-flex justify-center items-center size-12 rounded-full border-4 border-green-50 bg-green-100 text-green-500 dark:bg-green-700 dark:border-green-600 dark:text-green-100"
    >
          <svg
              xmlns="http://www.w3.org/2000/svg"
              class="shrink-0 size-8"
              fill="currentColor"
              viewBox="0 0 16 16">
            <path
                d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"
            />
          </svg>
    </span>
</div>
