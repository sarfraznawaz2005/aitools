<div x-data="{ open: true }">

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
                    chatContent.scrollTop = chatContent.scrollHeight + 1000;
                }
            }
            }"

            x-init="
                $nextTick(() => { focusInput(); scrollToBottom(); });
                Livewire.on('focusInput', () => { $nextTick(() => { scrollToBottom(); focusInput(); }); });
                Livewire.hook('message.received', () => scrollToBottom());
                Livewire.hook('message.processed', () => scrollToBottom());
            "

            x-intersect="$nextTick(() => { focusInput(); scrollToBottom(); })">

            <!-- Sidebar Content -->
            <div class="flex-1 overflow-y-auto relative px-6 w-full" x-ref="chatContent">

                <!-- Sidebar Header -->
                <div class="flex items-center sticky top-0 w-full bg-gray-100 p-3 px-4 rounded-lg">
                    <div class="flex-grow border-t mx-1 border-gray-300"></div>
                    <span class="uppercase text-xs px-1 text-gray-500 text-center font-semibold">
                        chat with {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders
                    </span>
                    <div class="flex-grow border-t mx-1 border-gray-300"></div>
                </div>

                @if(!$this->totalNotesCount)
                    <div class="p-4 text-center text-gray-500">
                        <p class="text-lg font-semibold">No notes found</p>
                        <p class="text-sm">Add notes to chat with them</p>
                    </div>
                @endif

                <!-- Chat content -->
                <div class="space-y-4 pt-2 px-4">
                    @foreach($conversation as $message)
                        @if($message['role'] === 'user')
                            <div class="flex flex-col" wire:key="note-message{{$message['timestamp'] . uniqid()}}">
                                <div
                                    class="bg-blue-100 text-gray-600 prose prose-sm sm:prose lg:prose xl:prose cursor-pointer text-sm px-3 py-1 rounded-lg border border-blue-200 rounded-br-none self-end max-w-full">
                                    {!! nl2br(e($message['content'])) !!}
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col" wire:key="note-message{{$message['timestamp'] . uniqid()}}">
                                <div
                                    class="bg-white note-message text-gray-800 prose prose-sm sm:prose lg:prose xl:prose cursor-pointer text-sm px-3 rounded-lg border border-gray-200 rounded-bl-none self-start max-w-full">
                                    <bdi>{!! $message['content'] !!}</bdi>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div x-data="{show:true}"
                         x-init="
                            $wire.on('goAhead', () => { console.log('here'); show = true; scrollToBottom(); Livewire.dispatch('getResponse'); });
                            $wire.on('focusInput', () => { show = false; scrollToBottom(); });
                         ">
                        <div x-show="show" class="flex flex-col">
                            <div
                                wire:stream="aiStreamResponse"
                                class="bg-white note-message text-gray-800 prose p-2 prose-sm sm:prose lg:prose xl:prose cursor-pointer text-sm px-3 rounded-lg border border-gray-200 rounded-bl-none self-start max-w-full">
                            </div>
                        </div>

                        <div x-show="show">
                            <div class="fixed inset-0 bg-transparent w-screen h-screen opacity-5 z-[70]"></div>
                            <div class="fixed bottom-[20%] right-[13.5%]">
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
                        </div>
                    </div>

                </div>
                <!-- Chat content End -->

            </div>

            <!-- Chat Input at the Bottom -->
            <div
                class="p-1 flex flex-col sm:flex-row bg-white items-center border border-gray-300 rounded-lg m-3 mx-4">
                <div class="w-full sm:w-auto">
                    <livewire:general.model-selector for="{{App\Constants::NOTES_SELECTED_LLM_KEY}}"/>
                </div>

                <div class="relative w-full mt-2 sm:mt-0">

                    @error('userMessage')
                    <div class="text-red-500 text-sm em p-1">{{ $message }}</div>
                    @enderror

                    <input type="text"
                           wire:model="userMessage"
                           x-ref="chatInput"
                           @keydown.enter="$wire.call('setMessage', $refs.chatInput.value)"
                           {{!hasApiKeysCreated() || !$this->totalNotesCount ? 'disabled' : ''}}
                           autofocus
                           autocomplete="off"
                           tabindex="0"
                           dir="auto"
                           wire:loading.attr="disabled"
                           class="py-2 z-0 pr-4 block w-full border-gray-300 border-transparent rounded-lg text-sm focus:border-transparent focus:ring-transparent disabled:opacity-50 disabled:pointer-events-none"
                           placeholder="Press enter to chat with your notes...">
                </div>

                <x-confirm-dialog call="resetConversation"
                                  x-data x-tooltip.raw="Reset Conversation"
                                  text="Are you sure you want reset the conversation?"
                                  class="inline-flex mr-2 mt-2 items-center text-sm border-transparent focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                    <x-icons.delete class="w-5 h-5 text-gray-500"/>
                </x-confirm-dialog>
            </div>
        </div>
    </div>

    <script>
        function setupSuggestedLinks() {
            function attachLinkEventListeners() {
                document.querySelectorAll('.ai-suggested-answer').forEach(link => {
                    link.removeEventListener('click', handleLinkClick); // Remove existing listener to avoid duplicates
                    link.addEventListener('click', handleLinkClick);
                });
            }

            function handleLinkClick(e) {
                e.preventDefault();
                Livewire.dispatch('suggestedAnswer', [e.target.textContent]);
            }

            // Attach initial event listeners
            attachLinkEventListeners();

            // MutationObserver to detect changes in the DOM
            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        attachLinkEventListeners();
                    }
                }
            });

            // Start observing the document body for changes
            observer.observe(document.body, {childList: true, subtree: true});
        }

        setupSuggestedLinks();
    </script>

</div>
