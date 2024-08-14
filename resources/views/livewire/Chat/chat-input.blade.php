<div class="w-full lg:left-64 lg:w-[calc(100%-16rem)] mx-auto fixed bottom-0 z-10 p-4 px-8 bg-gray-200 sm:bg-gray-50">
    <div class="lg:hidden flex mb-2 sm:mb-3">
        <!-- Sidebar Toggle -->
        <button type="button"
                class="p-2 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-application-sidebar"
                aria-label="Toggle navigation" data-hs-overlay="#hs-application-sidebar">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" x2="21" y1="6" y2="6"/>
                <line x1="3" x2="21" y1="12" y2="12"/>
                <line x1="3" x2="21" y1="18" y2="18"/>
            </svg>
            <span>Sidebar</span>
        </button>
        <!-- End Sidebar Toggle -->
    </div>

    <!-- Input -->
    <div class="flex w-full items-center" x-data="{
        lastQuery: '',
        adjustHeight() {
            $nextTick(() => {
                this.$refs.textarea.style.height = 'auto';
                const lines = this.$refs.textarea.value.split('\n').length;
                const lineHeight = parseInt(window.getComputedStyle(this.$refs.textarea).lineHeight);
                const maxHeight = lineHeight * 5;
                this.$refs.textarea.style.height = Math.min(this.$refs.textarea.scrollHeight, maxHeight) + 'px';
            });
        },
        handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.lastQuery = $wire.query;
                $wire.save();
            } else if (event.key === 'ArrowUp' && $wire.query === '') {
                event.preventDefault();
                $wire.query = this.lastQuery;
                this.$nextTick(() => {
                    this.$refs.textarea.selectionStart = this.$refs.textarea.selectionEnd = this.$refs.textarea.value.length;
                });
            }
        },
        init() {
            this.$watch('$wire.query', (value) => {
                this.adjustHeight();
            });

            Livewire.on('focusInput', () => {
                if (typeof this.$refs.textarea !== 'undefined' && this.$refs.textarea !== null) {
                    this.$refs.textarea.focus();
                }
            });
        }
    }"
         @submit-success="lastQuery = $wire.query">
        <div
            class="flex w-full flex-col gap-1.5 rounded p-1 transition-colors bg-gray-200 dark:bg-token-main-surface-secondary">
            <div class="flex items-end gap-1.5 md:gap-2">
                <div class="flex min-w-0 flex-1 flex-col">
                    @error('query')
                    <div class="text-red-500 text-sm em p-1">{{ $message }}</div>
                    @enderror

                    <textarea
                        x-ref="textarea"
                        name="query"
                        id="query"
                        wire:model="query"
                        @input="adjustHeight"
                        @paste="setTimeout(() => adjustHeight(), 0)"
                        @keydown="handleKeyDown"
                        wire:loading.attr="disabled"
                        tabindex="0"
                        autofocus
                        autocomplete="off"
                        dir="auto"
                        rows="1"
                        {{!hasApiKeysCreated() ? 'disabled' : ''}}
                        placeholder="Ask me anything..."
                        class="m-0 resize-none border-0 rounded px-4 focus:ring-0 focus-visible:ring-0 disabled:bg-gray-200"
                        style="height: 40px;"
                    ></textarea>
                </div>

                @if(!hasApiKeysCreated())
                    <button disabled
                            class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                        <x-icons.upload/>
                    </button>
                @else
                    <button
                        type="submit"
                        id="chatSubmitButton"
                        @click="lastQuery = $wire.query;"
                        :disabled="!$wire.query.trim()"
                        wire:loading.attr="disabled"
                        wire:click="save"
                        class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                        <x-icons.upload/>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:navigated', () => {
            const chatTextInput = document.getElementById('query');

            if (typeof chatTextInput !== 'undefined' && chatTextInput !== null) {
                chatTextInput.focus();
            }
        });
    </script>

</div>
