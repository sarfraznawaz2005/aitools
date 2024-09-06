<div x-data="{ lastQuery: '', userMessage: '' }" class="w-full h-full">

    <div
        class="relative w-full h-full bg-gray-50 flex flex-col rounded-lg"
        x-data="{
                lastQuery: '',
                focusInput() {
                    if ($refs.chatInput) {
                        $refs.chatInput.focus();
                    }
                },
                scrollToBottom() {
                    const chatContent = this.$refs.chatContent;
                    if (chatContent) {
                        chatContent.scrollTop = chatContent.scrollHeight;
                    }
                },
                handleKeyDown(event) {
                    if (event.key === 'Enter' && !event.shiftKey) {
                        event.preventDefault();
                        this.lastQuery = $refs.chatInput.value;
                        $wire.call('setMessage', $refs.chatInput.value);
                        $refs.chatInput.focus();
                    } else if (event.key === 'ArrowUp' && $refs.chatInput.value === '') {
                        event.preventDefault();
                        if (this.lastQuery) {
                            $refs.chatInput.value = this.lastQuery;
                            this.$nextTick(() => {
                                $refs.chatInput.selectionStart = $refs.chatInput.selectionEnd = $refs.chatInput.value.length;
                                $refs.chatInput.focus();
                            });
                        }
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

        <!-- Content -->
        <div class="flex-1 overflow-y-auto relative px-6 w-full" x-ref="chatContent">

            <!-- Header -->
            <div class="flex items-center w-full sticky top-0 bg-gray-50 py-3 rounded-lg">
                <div class="flex-grow border-t mx-1 border-gray-300"></div>
                <span class="uppercase text-xs px-1 text-gray-500 text-center font-semibold">
                        Quick Disposable Chat
                    </span>
                <div class="flex-grow border-t mx-1 border-gray-300"></div>
            </div>

            <!-- Chat content -->
            <div class="space-y-4 pt-2 px-4">

                @foreach($conversation as $index => $message)
                    <div x-data="{
                            copied: false,
                            copy () {
                              $clipboard($refs.message.innerText)
                              this.copied = true
                              setTimeout(() => {
                                this.copied = false
                              }, 1000)
                            }
                          }">
                        @if($message['role'] === 'user')
                            <div
                                class="flex flex-col" wire:key="note-message{{$index}}">
                                <div
                                    class="bg-blue-100 text-gray-600 prose prose-sm sm:prose lg:prose xl:prose text-sm px-3 py-1 rounded-2xl border border-blue-200 rounded-br-none self-end max-w-full">
                                    <bdi x-ref="message">{!! nl2br(e($message['content'])) !!}</bdi>
                                </div>
                            </div>

                            <!-- Button Group -->
                            <div class="flex justify-end mt-2 items-center">
                                <div>
                                    <button type="button"
                                            x-data x-tooltip.raw="Copy"
                                            @click="copy"
                                            class="ignore-mutation inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.copy class="hover:text-gray-600"/>
                                        <span
                                            x-text="typeof(copied) !== 'undefined' && copied ? 'Copied' : ''"></span>
                                    </button>
                                </div>
                                <div>
                                    <x-confirm-dialog call="deleteMessage({{$index}})" x-data
                                                      x-tooltip.raw="Delete"
                                                      class="inline-flex items-center ml-3 text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.delete class="size-4 text-gray-400 hover:text-red-600"/>
                                    </x-confirm-dialog>
                                </div>
                            </div>
                            <!-- End Button Group -->
                        @else
                            <div class="flex flex-col" wire:key="note-message{{$index}}">
                                <div
                                    class="bg-white note-message text-gray-800 prose prose-sm sm:prose lg:prose xl:prose text-sm px-3 rounded-2xl border border-gray-200 rounded-bl-none self-start max-w-full">
                                    <bdi x-ref="message">{!! $message['content'] !!}</bdi>
                                </div>
                            </div>

                            <!-- Button Group -->
                            <div class="flex mt-2 items-center">
                                <div>
                                    <button type="button"
                                            x-data x-tooltip.raw="Copy"
                                            @click="copy"
                                            class="ignore-mutation inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.copy class="hover:text-gray-600"/>
                                        <span
                                            x-text="typeof(copied) !== 'undefined' && copied ? 'Copied' : ''"></span>
                                    </button>
                                </div>
                                <div>
                                    <x-confirm-dialog call="deleteMessage({{$index}})" x-data
                                                      x-tooltip.raw="Delete"
                                                      class="inline-flex items-center mt-[-2px] ml-3 text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.delete class="size-4 text-gray-400 hover:text-red-600"/>
                                    </x-confirm-dialog>
                                </div>
                            </div>
                            <!-- End Button Group -->

                        @endif
                    </div>
                @endforeach

                <div x-data="{show:false}"
                     x-init="
                            $wire.on('goAhead', () => {
                                $refs.chatInput.disabled = true;
                                show = true;
                                scrollToBottom();
                                Livewire.dispatch('getResponse');
                            });

                            $wire.on('focusInput', () => {
                                show = false;
                                scrollToBottom();
                                $refs.chatInput.disabled = false;
                            });
                         ">

                    <div :style="show ? 'visibility: visible;' : 'visibility: hidden;'" class="flex flex-col">
                        <div
                            wire:stream="aiStreamResponse"
                            class="bg-white note-message text-gray-800 prose p-2 prose-sm sm:prose lg:prose xl:prose text-sm px-3 rounded-lg border border-gray-200 rounded-bl-none self-start max-w-full">
                            <span class="animate-ping text-2xl">🤖</span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Chat content End -->

        </div>

        <!-- Chat Input at the Bottom -->

        <div class="flex justify-between items-center w-full px-4">
            <div
                class="p-1 flex flex-col sm:flex-row bg-white items-center w-full border border-r-0 rounded-tr-none rounded-br-none border-gray-300 rounded-lg m-3 mr-0">
                <div class="w-full sm:w-auto">
                    <livewire:general.model-selector for="{{App\Constants::NOTES_SELECTED_LLM_KEY}}"/>
                </div>

                <div class="relative w-full mt-2 sm:mt-0">

                    <input type="text"
                           wire:ignore
                           x-model="userMessage"
                           wire:model="userMessage"
                           x-ref="chatInput"
                           @keydown="handleKeyDown"
                           {{!hasApiKeysCreated() ? 'disabled' : ''}}
                           autofocus
                           autocomplete="off"
                           tabindex="0"
                           dir="auto"
                           wire:loading.attr="disabled"
                           class="py-2 z-0 pr-12 block w-full border-gray-300 border-transparent rounded-lg text-sm focus:border-transparent focus:ring-transparent disabled:opacity-50 disabled:pointer-events-none"
                           placeholder="Chat with your notes...">

                    <button type="button"
                            x-data x-tooltip.raw="Send Message"
                            @click="
                                    $wire.call('setMessage', $refs.chatInput.value);
                                    $refs.chatInput.focus();
                                "
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div
                class="bg-gray-200 mr-4 border border-gray-300 rounded-lg pl-4 border-l-0 p-1.5 rounded-tl-none rounded-bl-none">
                <x-confirm-dialog call="resetConversation"
                                  x-data x-tooltip.raw="Reset Conversation"
                                  text="Are you sure you want reset the conversation?"
                                  class="inline-flex mr-2 mt-2 items-center text-sm border-transparent focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                    <x-icons.delete class="w-5 h-5 text-gray-400 hover:text-red-600"/>
                </x-confirm-dialog>
            </div>
        </div>
        @error('userMessage')
        <div class="text-red-500 text-sm em p-1 flex justify-center items-center mt-[-14px]">{{ $message }}</div>
        @enderror

    </div>

    <script>
        function setupSuggestedLinks() {
            // Function to decode escaped HTML entities
            function decodeHTMLEntities(text) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(text, "text/html");
                return doc.documentElement.textContent;
            }

            // Function to convert both escaped and non-escaped related_question tags to links
            function convertRelatedQuestionsToLinks() {
                document.querySelectorAll('.note-message li').forEach(li => {
                    // First, handle escaped related_question tags
                    if (li.innerHTML.includes('&lt;related_question&gt;')) {
                        const decodedHTML = decodeHTMLEntities(li.innerHTML);

                        // Replace related_question tags in the decoded HTML
                        // Update the li element with the new HTML (converted links)
                        li.innerHTML = decodedHTML.replace(/&lt;related_question&gt;/g, '<a class="ai-suggested-answer text-blue-500 hover:text-blue-700 cursor-pointer block text-sm" href="#">')
                            .replace(/&lt;\/related_question&gt;/g, '</a>');
                    }

                    // Then handle non-escaped related_question tags
                    if (li.innerHTML.includes('<related_question>')) {
                        // Replace non-escaped related_question tags directly
                        // Update the li element with the new HTML (converted links)
                        li.innerHTML = li.innerHTML.replace(/<related_question>/g, '<a class="ai-suggested-answer block text-blue-500 hover:text-blue-700 cursor-pointer text-sm" href="#">')
                            .replace(/<\/related_question>/g, '</a>');
                    }
                });
            }

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

            // Convert related_question elements (escaped or not) to links initially
            convertRelatedQuestionsToLinks();
            // Attach initial event listeners to the links
            attachLinkEventListeners();

            // MutationObserver to detect changes in the DOM
            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        // Re-run the conversion and listener attachment when new nodes are added
                        convertRelatedQuestionsToLinks();
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
