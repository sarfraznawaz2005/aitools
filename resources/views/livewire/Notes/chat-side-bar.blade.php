<div x-data="{ open: false }">

    <button type="button" @click="open = true"
            class="py-2 px-4 mr-2 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border-transparent bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
        <x-icons.chat class="size-4"/>
        Chat With Your Notes
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform translate-x-full"
         class="fixed inset-0 z-[60] top-14 flex justify-end sm:justify-center md:justify-end">
        <div
            @click.away="open = false"
            class="relative w-full max-w-md h-full bg-gray-100 shadow-2xl flex flex-col rounded-lg" x-data="{
            focusInput() {
                if (open) {
                    $refs.chatInput.focus();
                }

                this.scrollToBottom();
            },
            scrollToBottom() {
                const chatContent = this.$refs.chatContent;

                if (chatContent && open) {
                    chatContent.scrollTop = chatContent.scrollHeight;
                }
            }
            }"

            x-init="
                $nextTick(() => { focusInput(); scrollToBottom(); });
                Livewire.on('focusInput', () => { $nextTick(() => { scrollToBottom(); focusInput(); }); });
            "

            x-intersect="$nextTick(() => { focusInput(); scrollToBottom(); })">

            <!-- Sidebar Content -->
            <div class="flex-1 overflow-y-auto relative" x-ref="chatContent">
                <div class="flex items-center sticky top-0 w-full bg-gray-100 py-3 rounded-lg">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="uppercase text-xs px-1 text-gray-500 text-center">
                        chat with {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders
                    </span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                @if(!$this->totalNotesCount)
                    <div class="p-4 text-center text-gray-500">
                        <p class="text-lg font-semibold">No notes found</p>
                        <p class="text-sm">Add notes to chat with them</p>
                    </div>
                @endif

                <!-- Chat content -->
                <div class="space-y-4 pt-20 px-4">
                    @foreach($conversation as $message)
                        @if($message['role'] === 'user')
                            <div class="flex flex-col">
                                <div
                                    class="bg-blue-100 text-gray-600 text-sm p-3 rounded-lg border border-blue-200 rounded-br-none self-end max-w-full">
                                    {{ $message['content'] }}
                                </div>
                                <span
                                    class="text-xs text-gray-500 mt-1 self-end">You • {{ $message['timestamp'] }}
                                </span>
                            </div>
                        @else
                            <div class="flex flex-col">
                                <div
                                    class="bg-white text-gray-800 text-sm p-3 rounded-lg border border-gray-200 rounded-bl-none self-start max-w-full">
                                    {{ $message['content'] }}
                                </div>
                                <span
                                    class="text-xs text-gray-500 mt-1">AI Assistant • {{ $message['timestamp'] }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <!-- Chat content End -->

            </div>

            <!-- Chat Input at the Bottom -->
            <div
                class="p-2 flex flex-col sm:flex-row bg-white items-center border border-gray-300 rounded-lg m-3 mx-4">
                <div class="w-full sm:w-auto">
                    <livewire:general.model-selector for="{{App\Constants::NOTES_SELECTED_LLM_KEY}}"/>
                </div>

                <div class="relative w-full mt-2 sm:mt-0">

                    @error('userMessage')
                    <div class="text-red-500 text-sm em p-1">{{ $message }}</div>
                    @enderror

                    <form wire:submit.prevent="sendMessage"
                          @submit.prevent="$nextTick(() => { focusInput(); scrollToBottom(); })"
                          class="relative w-full mt-2 sm:mt-0">

                        <input type="text"
                               wire:model="userMessage"
                               x-ref="chatInput"
                               {{!hasApiKeysCreated() || !$this->totalNotesCount ? 'disabled' : ''}}
                               autofocus
                               autocomplete="off"
                               tabindex="0"
                               dir="auto"
                               wire:loading.attr="disabled"
                               class="py-2 pr-10 block w-full border-gray-300 border-transparent rounded-lg text-sm focus:border-transparent focus:ring-transparent disabled:opacity-50 disabled:pointer-events-none"
                               placeholder="Ask me anything about your notes...">

                        <button type="submit"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
