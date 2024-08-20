<div class="py-20 px-4 sm:px-6 md:px-8 chatlist mx-auto max-w-7xl w-full">

    <x-flash/>

    <ul class="space-y-5 w-full max-w-none">

        @if(!hasApiKeysCreated())
            <li class="mb-5">
                <livewire:apikeys.api-key-banner/>
            </li>

            <script>
                function scrollToBottom() {
                    // don't remove this function
                }
            </script>
        @else

            <livewire:chat.bot-forward wire:key="chatbuddy-bot-forward"/>

            @unless (isset($messages))
                <livewire:chat.bot-selector wire:key="chatbuddy-bot-selector"/>

                <script>
                    function scrollToBottom() {
                        // don't remove this function
                    }
                </script>
            @else

                <script>
                    function scrollToBottom() {
                        window.scrollTo({
                            top: document.body.scrollHeight + 10000,
                            behavior: 'smooth'
                        });
                    }
                </script>

                <li>
                    <div class="flex justify-center align-center">
                    <span
                        class="whitespace-nowrap inline-block py-1.5 px-3 rounded-lg border border-gray-200 font-medium bg-gray-100 text-gray-500 text-xs sm:text-sm md:text-base lg:text-base">
                        📅 Conversation created {{$conversation->created_at->diffForHumans()}}
                    </span>
                    </div>
                </li>

                @if($botFiles)
                    <fieldset
                        class="items-center justify-center w-full bg-gray-100 border border-gray-300 text-center rounded-lg p-3 dark:border-neutral-700 my-4">
                        <legend class="text-sm text-gray-600 dark:text-neutral-300 font-semibold">
                            Chatting With Uploaded Files
                        </legend>
                        @foreach($botFiles as $file)
                            <div class="text-center mb-2" style="font-size: .8rem;">
                                <div>{{basename($file)}}</div>
                            </div>
                        @endforeach
                    </fieldset>
                @endif


                @if (count($messages) > 1)
                    <li class="flex justify-center ignore-mutation">

                        @if(isset($conversation) && $conversation)

                            <div class="flex justify-end w-full fixed top-16 right-10 gap-x-2">

                                <button type="button"
                                        x-tooltip.raw="Clear Conversation"
                                        x-data="{ isConfirming: false }"
                                        @click="isConfirming ? $wire.clearConversation() : isConfirming = true"
                                        @click.outside="isConfirming = false"
                                        :class="{
                                             'bg-white text-gray-800 hover:bg-gray-50': !isConfirming,
                                             'bg-red-500 text-white hover:bg-red-500': isConfirming
                                        }"
                                        class="py-2 px-4 flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 shadow-sm focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                                    <x-icons.delete class="flex-shrink-0"/>
                                    <span
                                          x-text="isConfirming ? 'Confirm?' : 'Clear'"
                                          class="flex-grow text-center" :class="{'pr-6': isConfirming}"></span>
                                </button>

                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        type="button"
                                        class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                                    >
                                        <x-icons.export class="shrink-0 size-4"/>
                                        Export
                                        <svg
                                            class="size-4"
                                            :class="{ 'rotate-180': open }"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="m6 9 6 6 6-6"/>
                                        </svg>
                                    </button>

                                    <div
                                        x-cloak
                                        x-show="open"
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 mt-2 min-w-28 rounded-lg bg-gray-50 shadow-md space-y-0.5 divide-y divide-gray-200"
                                        role="menu"
                                    >
                                        <div class="py-2 first:pt-0 last:pb-0">
                                            <a
                                                @click.prevent="$wire.export('html')"
                                                class="flex items-center rounded-lg gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                                href="#"
                                            >
                                                <x-icons.code class="shrink-0 size-4"/>
                                                HTML
                                            </a>
                                            <a
                                                @click.prevent="$wire.export('txt')"
                                                class="flex items-center rounded-lg gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                                href="#"
                                            >
                                                <x-icons.text class="shrink-0 size-4"/>
                                                TEXT
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endif

                @foreach($messages as $message)
                    @if(!$message->is_ai)
                        <li wire:key="chatlist-message{{$message->id}}" class="my-4" x-data="{
                            copied: false,
                            copy () {
                              $clipboard($refs.content.innerText)
                              this.copied = true
                              setTimeout(() => {
                                this.copied = false
                              }, 1000)
                            }
                          }">
                            <div class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
                                <div
                                    class="inline-block bg-blue-100 rounded-lg px-4 py-2 shadow-sm border border-blue-200">
                                        <span class="text-gray-600 text-xs sm:text-sm md:text-base lg:text-base"
                                              x-ref="content">
                                            {!! nl2br(e($message->body)) !!}
                                        </span>
                                </div>
                            </div>

                            <!-- Button Group -->
                            <div class="flex justify-end mt-2">
                                <div>
                                    <button type="button"
                                            x-data x-tooltip.raw="Copy"
                                            @click="copy"
                                            class="ignore-mutation inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.copy class="hover:text-gray-600"/>
                                        <span x-text="copied ? 'Copied' : ''"></span>
                                    </button>
                                </div>
                                <div>
                                    <x-confirm-dialog call="deleteMessage({{$message->id}})" x-data
                                                      x-tooltip.raw="Delete"
                                                      class="inline-flex items-center ml-3 text-sm rounded-full border border-transparent text-gray-500">
                                        <x-icons.delete class="size-4 text-gray-400 hover:text-gray-500"/>
                                    </x-confirm-dialog>
                                </div>
                            </div>
                            <!-- End Button Group -->
                        </li>
                    @else
                        <li wire:key="chatlist-message{{$message->id}}" class="flex gap-x-2 sm:gap-x-4 my-8" x-data="{
                            copied: false,
                            copy () {
                              $clipboard($refs.content.innerText)
                              this.copied = true
                              setTimeout(() => {
                                this.copied = false
                              }, 1000)
                            }
                          }">
                            <div class="grow w-full max-w-none space-y-3">
                                <!-- Card -->
                                <div class="bg-white border border-gray-200 rounded-lg px-4 py-2">
                                    <div x-ref="content"
                                         class="text-gray-500 aibot-message-content prose prose-sm sm:prose lg:prose xl:prose max-w-none w-full word-break-all break-long-words scrollbar-code">
                                        {!! $message->body !!}
                                    </div>
                                </div>
                                <!-- End Card -->

                                <div class="flex justify-between items-center">

                                    <div class="inline-block mt-[-20px] ml-2">
                                        <span class="text-gray-400 text-xs">{{$message->llm}}</span>
                                    </div>

                                    @if($loop->last && !$botFiles)
                                        <div class="inline-block mt-[-12px] cursor-pointer text-xl"
                                             wire:click="forceAnswer({{$message->id}})"
                                             x-data
                                             x-tooltip.raw="Stuck with AI giving same answer repeatedly? Click to try to get forced answer.">
                                            🧙‍♂️
                                        </div>
                                    @endif

                                    <!-- Button Group -->
                                    <div class="flex justify-end">
                                        <div class="mt-[-5px]">
                                            <button type="button"
                                                    x-data x-tooltip.raw="Copy"
                                                    @click="copy"
                                                    class="ignore-mutation inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                                <x-icons.copy class="hover:text-gray-600"/>
                                                <span x-text="copied ? 'Copied' : ''"></span>
                                            </button>

                                            <x-confirm-dialog call="deleteMessage({{$message->id}})" x-data
                                                              x-tooltip.raw="Delete"
                                                              class="inline-flex items-center ml-2 text-sm rounded-full border border-transparent text-gray-500">
                                                <x-icons.delete class="size-4 text-gray-400 hover:text-gray-500"/>
                                            </x-confirm-dialog>

                                            @if($loop->last)
                                                <button type="button"
                                                        wire:click="regenerate({{$message->id}})"
                                                        x-data x-tooltip.raw="Regenerate"
                                                        class="inline-flex items-center ml-2 text-sm rounded-full border border-transparent text-gray-500">
                                                    <x-icons.refresh
                                                        class="size-5 text-gray-500 hover:text-gray-600"/>
                                                </button>

                                                <button type="button"
                                                        wire:click="$dispatch('startFoward', [{{$message->id}}])"
                                                        x-data x-tooltip.raw="Forward to another bot"
                                                        class="inline-flex items-center ml-2 mr-1 text-sm rounded-full border border-transparent text-gray-500">
                                                    <x-icons.share
                                                        class="size-5 text-gray-400 hover:text-gray-600"/>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- End Button Group -->
                                </div>

                            </div>
                        </li>
                    @endif
                @endforeach
            @endunless
        @endif
    </ul>

    <script data-navigate-once>

        function decodeUnicode(str) {
            return str.replace(/\\u[\dA-F]{4}/gi, function (match) {
                return String.fromCodePoint(parseInt(match.replace(/\\u/g, ''), 16));
            });
        }

        function openLinkExternally() {
            /*
            document.querySelectorAll('.aibot-message-content a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    require('electron').shell.openExternal(link.href);
                });
            });
            */
        }

        function observeChatList() {
            const chatList = document.querySelector('.chatlist ul');
            if (!chatList) return;

            const observer = new MutationObserver((mutationsList) => {
                for (const mutation of mutationsList) {
                    if (mutation.target.closest('.ignore-mutation')) {
                        return;
                    }
                }

                performCommonPageActions();
            });

            observer.observe(chatList, {childList: true, subtree: true});
        }

        function performCommonPageActions() {
            scrollToBottom();
            //openLinkExternally()
        }

        function performInProgressActions() {
            performCommonPageActions();
        }

        function performDoneActions() {
            Livewire.dispatch('refreshChatList');

            const chatTextInput = document.getElementById('query');

            if (typeof chatTextInput !== 'undefined' && chatTextInput !== null) {
                chatTextInput.removeAttribute('disabled');
                chatTextInput.focus();
            }

            // order is important
            performCommonPageActions();

            setTimeout(() => {
                Livewire.dispatch('hideLoading');
                scrollToBottom();
            }, 1000);
        }

        document.addEventListener('livewire:navigated', () => {
            scrollToBottom();
        });

        document.addEventListener('livewire:navigated', () => {
            performCommonPageActions();
            observeChatList();

            Livewire.hook('message.received', () => performInProgressActions());
            Livewire.hook('message.processed', () => performDoneActions());

            window.Livewire.on('getChatBuddyAiResponse', ($conversationId) => {

                Livewire.dispatch('showLoading');

                const chatTextInput = document.getElementById('query');
                chatTextInput.setAttribute('disabled', 'disabled');

                performInProgressActions();

                const source = new EventSource("/chat-buddy/chat/" + $conversationId);
                source.addEventListener("update", function (event) {
                    // strangely, streaming does not show up correctly in this way
                    //const lastMessage = document.querySelector('.aibot-message-content:last-child');

                    const messageElements = document.querySelectorAll('.aibot-message-content');
                    const lastMessage = messageElements[messageElements.length - 1];

                    performInProgressActions();

                    lastMessage.innerHTML = lastMessage.textContent.replace("{{App\Constants::CHATBUDDY_LOADING_STRING}}", "");

                    if (event.data === "<END_STREAMING_SSE>") {
                        source.close();
                        console.log("SSE closed");
                        performDoneActions();
                        return;
                    }

                    const decodedData = decodeUnicode(JSON.parse(event.data));
                    lastMessage.innerHTML += decodedData;

                    performInProgressActions();
                });

                source.addEventListener("error", function () {
                    source.close();
                    performDoneActions();
                    console.log("SSE closed due to error");
                });
            })
        }, {once: true});
    </script>

</div>
