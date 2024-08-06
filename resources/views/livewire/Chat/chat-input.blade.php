<!-- Input -->
<div class="flex w-full items-center" x-data="{
        query: '',
        adjustHeight() {
            this.$refs.textarea.style.height = 'auto';
            const lines = this.$refs.textarea.value.split('\n').length;
            const lineHeight = parseInt(window.getComputedStyle(this.$refs.textarea).lineHeight);
            const maxHeight = lineHeight * 5;
            this.$refs.textarea.style.height = Math.min(this.$refs.textarea.scrollHeight, maxHeight) + 'px';
        }
    }">
    <div class="flex w-full flex-col gap-1.5 rounded p-1 transition-colors bg-gray-200 dark:bg-token-main-surface-secondary">
        <div class="flex items-end gap-1.5 md:gap-2">

            <div class="flex min-w-0 flex-1 flex-col">
                            <textarea
                                x-ref="textarea"
                                x-model="query"
                                x-on:input="adjustHeight()"
                                x-on:paste="setTimeout(() => adjustHeight(), 0)"
                                tabindex="0"
                                autofocus
                                autocomplete="off"
                                dir="auto"
                                rows="1"
                                placeholder="Ask me anything..."
                                class="m-0 resize-none border-0 rounded px-4 focus:ring-0 focus-visible:ring-0"
                                style="height: 40px;"></textarea>
            </div>

            @if(!App\Models\ApiKey::hasApiKeys())
                <button wire:offline.attr="disabled" disabled class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32" class="icon-2xl">
                        <path fill="currentColor" fill-rule="evenodd" d="M15.192 8.906a1.143 1.143 0 0 1 1.616 0l5.143 5.143a1.143 1.143 0 0 1-1.616 1.616l-3.192-3.192v9.813a1.143 1.143 0 0 1-2.286 0v-9.813l-3.192 3.192a1.143 1.143 0 1 1-1.616-1.616z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @else
                <button wire:offline.attr="disabled" :disabled="!query.trim()" class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32" class="icon-2xl">
                        <path fill="currentColor" fill-rule="evenodd" d="M15.192 8.906a1.143 1.143 0 0 1 1.616 0l5.143 5.143a1.143 1.143 0 0 1-1.616 1.616l-3.192-3.192v9.813a1.143 1.143 0 0 1-2.286 0v-9.813l-3.192 3.192a1.143 1.143 0 1 1-1.616-1.616z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endif


        </div>
    </div>
</div>
<!-- End Input -->
