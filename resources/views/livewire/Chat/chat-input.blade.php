<div class="w-full lg:left-64 lg:w-[calc(100%-16rem)] mx-auto fixed bottom-0 z-10 p-4 px-12 bg-gray-200 sm:bg-gray-50">
    <div class="lg:hidden flex justify-end mb-2 sm:mb-3">
        <!-- Sidebar Toggle -->
        <button type="button" class="p-2 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-application-sidebar" aria-label="Toggle navigation" data-hs-overlay="#hs-application-sidebar">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" x2="21" y1="6" y2="6" />
                <line x1="3" x2="21" y1="12" y2="12" />
                <line x1="3" x2="21" y1="18" y2="18" />
            </svg>
            <span>Sidebar</span>
        </button>
        <!-- End Sidebar Toggle -->
    </div>

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
            <form autocomplete="off" wire:submit="save">
                <div class="flex items-end gap-1.5 md:gap-2">
                        <div class="flex min-w-0 flex-1 flex-col">

                            <textarea
                                x-ref="textarea"
                                name="query"
                                id="query"
                                wire:model="query"
                                x-model="query"
                                x-on:input="adjustHeight()"
                                x-on:paste="setTimeout(() => adjustHeight(), 0)"
                                tabindex="0"
                                autofocus
                                autocomplete="off"
                                dir="auto"
                                rows="1"
                                {{!hasApiKeysCreated() ? 'disabled' : ''}}"
                                placeholder="Ask me anything..."
                                class="m-0 resize-none border-0 rounded px-4 focus:ring-0 focus-visible:ring-0"
                                style="height: 40px;"></textarea>
                        </div>

                    @if(!hasApiKeysCreated())
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
            </form>
        </div>
    </div>
</div>
<!-- End Input -->


