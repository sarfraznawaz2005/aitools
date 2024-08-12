<div class="py-20 px-8">

    <ul class="space-y-5">

        @if(!hasApiKeysCreated())
            <li class="mb-5">
                <livewire:apikeys.api-key-banner/>
            </li>
        @else

            @if ($messages)

                <div class="flex justify-center align-center">
                    <span
                        class="whitespace-nowrap inline-block py-1.5 px-3 rounded-lg font-medium bg-gray-100 text-gray-500">
                        📅 Conversation created {{$conversation->created_at->diffForHumans()}}
                    </span>
                </div>

                @forelse($messages as $message)
                    <div wire:key="{{$message->id}}">

                        @if(!$message->is_ai)
                            <li class="my-4" x-data="{
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
                                    <div class="inline-block bg-gray-200 rounded-lg px-4 py-2 shadow-sm">
                                        <p class="text-gray-600" style="font-size: 1rem; line-height: 1.8rem;"
                                           x-ref="content">
                                            {{$message->body}}
                                        </p>
                                    </div>
                                </div>

                                <!-- Button Group -->
                                <div class="flex justify-end mt-2">
                                    <div>
                                        <button type="button"
                                                x-data x-tooltip.raw="Copy"
                                                @click="copy"
                                                class="inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                            <x-icons.copy class="hover:text-gray-600"/>
                                            <span x-text="copied ? 'Copied' : ''"></span>
                                        </button>
                                    </div>
                                    <div>
                                        <x-confirm-dialog :id="$message->id" using="deleteMessage" x-data
                                                          x-tooltip.raw="Delete"
                                                          class="inline-flex items-center ml-3 text-sm rounded-full border border-transparent text-gray-500">
                                            <x-icons.delete class="size-4 text-gray-400 hover:text-gray-500"/>
                                        </x-confirm-dialog>
                                    </div>
                                </div>
                                <!-- End Button Group -->
                            </li>
                        @else
                            <li class="flex gap-x-2 sm:gap-x-4 my-8" x-data="{
                                copied: false,
                                copy () {
                                  $clipboard($refs.content.innerText)
                                  this.copied = true
                                  setTimeout(() => {
                                    this.copied = false
                                  }, 1000)
                                }
                              }">
                                <div class="grow w-full space-y-3">
                                    <!-- Card -->
                                    <div
                                        class="bg-white border border-gray-200 rounded-lg px-4 py-1 space-y-2 dark:bg-neutral-900 dark:border-neutral-700">

                                        @if($loop->last)
                                            <div class="relative hidden" id="indicator" x-cloak>
                                                <span class="flex absolute size-5 mt-3 right-0">
                                                    <span
                                                        class="animate-ping absolute inline-flex size-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full size-5 bg-green-500"></span>
                                                </span>
                                            </div>
                                        @endif

                                        <p>
                                            <x-markdown x-ref="content" class="text-gray-600 aibot-message-content"
                                                        style="font-size: 1rem; line-height: 1.8rem;">
                                                {!! $message->body !!}
                                            </x-markdown>
                                        </p>
                                    </div>
                                    <!-- End Card -->

                                    <!-- Button Group -->
                                    <div class="flex justify-end">
                                        <div class="mt-[-5px]">
                                            <button type="button"
                                                    x-data x-tooltip.raw="Copy"
                                                    @click="copy"
                                                    class="inline-flex items-center text-sm rounded-full border border-transparent text-gray-500">
                                                <x-icons.copy class="hover:text-gray-600"/>
                                                <span x-text="copied ? 'Copied' : ''"></span>
                                            </button>

                                            <x-confirm-dialog :id="$message->id" using="deleteMessage" x-data
                                                              x-tooltip.raw="Delete"
                                                              class="inline-flex items-center ml-2 text-sm rounded-full border border-transparent text-gray-500">
                                                <x-icons.delete class="size-4 text-gray-400 hover:text-gray-500"/>
                                            </x-confirm-dialog>

                                            @if($loop->last)
                                                <button type="button"
                                                        wire:click="regenerate({{$message->id}})"
                                                        x-data x-tooltip.raw="Regenerate"
                                                        class="inline-flex items-center ml-2 text-sm rounded-full border border-transparent text-gray-500">
                                                    <x-icons.refresh class="size-5 text-gray-500 hover:text-gray-600"/>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- End Button Group -->
                                </div>
                            </li>
                        @endif

                    </div>
                @empty

                    <div
                        class="fixed inset-0 m-auto w-full lg:left-32 h-64 flex items-center justify-center text-gray-300 text-3xl font-bold">
                        Start New Conversation
                    </div>

                @endforelse
            @else
                <div
                    class="fixed inset-0 m-auto w-full lg:left-32 h-64 flex items-center justify-center text-gray-300 text-3xl font-bold">
                    Start New Conversation
                </div>
            @endif
        @endif

    </ul>

    <script>
        function decodeUnicode(str) {
            return str.replace(/\\u[\dA-F]{4}/gi, function (match) {
                return String.fromCodePoint(parseInt(match.replace(/\\u/g, ''), 16));
            });
        }

        function openLinkExternally() {
            document.querySelectorAll('.aibot-message-content a').forEach(link => {
                link.setAttribute('target', '_blank');
            });

            /*
            document.querySelectorAll('.aibot-message-content a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    require('electron').shell.openExternal(link.href);
                });
            });
            */
        }

        function performCommonPageActions() {
            window.scrollTo({
                top: document.body.scrollHeight + 100000,
                behavior: 'smooth'
            });

            openLinkExternally();
        }

        function performInProgressActions() {
            performCommonPageActions();
        }

        function performDoneActions() {
            performCommonPageActions();

            Livewire.dispatch('refreshChatList');

            const chatTextInput = document.getElementById('query');
            chatTextInput.removeAttribute('disabled');
            chatTextInput.focus();
        }

        window.addEventListener('DOMContentLoaded', () => {

            performCommonPageActions();

            Livewire.hook('message.received', () => performInProgressActions());

            document.addEventListener('livewire:navigated', () => performInProgressActions());

            window.Livewire.on('getAiResponse', ($conversationId) => {

                const chatTextInput = document.getElementById('query');
                chatTextInput.setAttribute('disabled', 'disabled');

                performInProgressActions();

                const source = new EventSource("/chat-buddy/chat/" + $conversationId);
                source.addEventListener("update", function (event) {
                    const lastMessage = document.querySelector('.aibot-message-content:last-child');
                    const indicator = document.getElementById('indicator');

                    performInProgressActions();

                    indicator.style.display = 'block';

                    lastMessage.innerHTML = lastMessage.textContent.replace("{{App\Constants::CHATBUDDY_LOADING_STRING}}", "");

                    if (event.data === "<END_STREAMING_SSE>") {
                        source.close();
                        console.log("SSE closed");
                        indicator.style.display = 'none';

                        performDoneActions();

                        return;
                    }

                    const decodedData = decodeUnicode(JSON.parse(event.data));
                    lastMessage.innerHTML += decodedData;

                    performInProgressActions();
                });

                source.addEventListener("error", function () {
                    source.close();
                    const indicator = document.getElementById('indicator');
                    indicator.style.display = 'none';
                    console.log("SSE closed due to error");
                    performDoneActions();
                });
            })
        });
    </script>

</div>
