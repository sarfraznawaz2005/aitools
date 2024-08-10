<div class="py-20 px-8">

    <ul class="space-y-5">

        @if(!hasApiKeysCreated())
            <li class="mb-5">
                <livewire:apikeys.api-key-banner/>
            </li>
        @else

            @if ($messages)
                @forelse($messages as $message)
                    <div wire:key="{{$message->id}}">

                        @if(!$message->is_ai)
                            <li class="my-4">
                                <div class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
                                    <div class="inline-block bg-gray-200 rounded-lg px-4 py-2 shadow-sm">
                                        <p class="text-gray-600" style="font-size: 1rem; line-height: 1.8rem;">
                                            {{$message->body}}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-gray-500 flex justify-end text-sm">
                                    {{isset($message->created_at) ? $message->created_at->diffForHumans() : ''}}
                                </div>
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
                                        class="bg-white border border-gray-200 rounded-lg px-4 py-2 space-y-2 dark:bg-neutral-900 dark:border-neutral-700">
                                        <p>
                                            <x-markdown x-ref="content" class="text-gray-600 aibot-message-content"
                                                        style="font-size: 1rem; line-height: 1.8rem;">
                                                {!! $message->body !!}
                                            </x-markdown>
                                        </p>
                                    </div>
                                    <!-- End Card -->

                                    <!-- Button Group -->
                                    <div class="sm:flex sm:justify-between">
                                        <div class="text-gray-500 flex justify-end text-sm mt-[-5px]">
                                            {{isset($message->created_at) ? $message->created_at->diffForHumans() : ''}}
                                        </div>
                                        <div class="mt-[-5px]">
                                            <button type="button"
                                                    @click="copy"
                                                    class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                                <x-icons.copy/>
                                                <span x-text="copied ? 'Copied' : 'Copy'"></span>
                                            </button>
                                            <button type="button"
                                                    class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                                <x-icons.refresh/>
                                                Regenerate
                                            </button>
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
                        start a fresh conversation!
                    </div>

                @endforelse
            @else
                <div
                    class="fixed inset-0 m-auto w-full lg:left-32 h-64 flex items-center justify-center text-gray-300 text-3xl font-bold">
                    start a fresh conversation!
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

        function scrollPageToBottom() {
            window.scrollTo({
                top: document.body.scrollHeight + 1000,
                behavior: 'smooth'
            });
        }

        // make all links inside any .aibot-message-content open in default browser
        function openLinkExternally() {
            document.querySelectorAll('.aibot-message-content a').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    require('electron').shell.openExternal(link.href);
                });
            });

            document.querySelectorAll('.aibot-message-content a').forEach(link => {
                link.setAttribute('target', '_blank');
            });
        }

        window.addEventListener('DOMContentLoaded', () => {

            scrollPageToBottom();
            openLinkExternally();

            // livewire message listener
            Livewire.hook('message.received', () => scrollPageToBottom());

            window.Livewire.on('getAiResponse', ($conversationId) => {

                scrollPageToBottom();

                const source = new EventSource("/chat-buddy/chat/" + $conversationId);
                source.addEventListener("update", function (event) {

                    const messageElements = document.querySelectorAll('.aibot-message-content');
                    const lastMessage = messageElements[messageElements.length - 1];

                    lastMessage.innerHTML = lastMessage.innerHTML.replace("Loading...", "");

                    if (event.data === "<END_STREAMING_SSE>") {
                        source.close();
                        console.log("SSE closed");
                        // window.location.reload();
                        lastMessage.innerHTML = marked.parse(lastMessage.textContent);
                        scrollPageToBottom();
                        return;
                    }

                    const decodedData = decodeUnicode(JSON.parse(event.data));
                    lastMessage.innerHTML += decodedData;

                    scrollPageToBottom();
                });

                source.addEventListener("error", function (event) {
                    source.close();
                    console.log("SSE closed due to error");
                });
            })
        });
    </script>

</div>
