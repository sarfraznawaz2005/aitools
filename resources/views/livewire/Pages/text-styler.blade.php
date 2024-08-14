<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    @if (hasApiKeysCreated())

        <div class="fixed left-3 bottom-3 inline-flex">
            <div
                class="sticky bottom-0 border-gray-200 rounded-lg dark:border-neutral-700 bg-gray-200 dark:bg-neutral-900 p-1">
                <livewire:general.model-selector for="{{App\Constants::TEXTSTYLER_SELECTED_LLM_KEY}}"
                                                 classes="rounded-lg"/>
            </div>
        </div>

        <div class="flex justify-center items-center w-full">
            <div class="w-full max-w-4xl">
                <div class="relative">
                    <textarea
                        wire:model="text"
                        id="hs-floating-textarea-gray"
                        class="peer p-4 block w-full border-gray-300 rounded-lg text-base placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600
                            focus:pt-6
                            focus:pb-2
                            [&:not(:placeholder-shown)]:pt-6
                            [&:not(:placeholder-shown)]:pb-2
                            autofill:pt-6
                            autofill:pb-2"
                        placeholder="Type your text..."></textarea>

                    <label
                        for="hs-floating-textarea-gray"
                        class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-white peer-disabled:opacity-50 peer-disabled:pointer-events-none
                          peer-focus:text-xs
                          peer-focus:-translate-y-1.5
                          peer-focus:text-gray-500 dark:peer-focus:text-neutral-500
                          peer-[:not(:placeholder-shown)]:text-xs
                          peer-[:not(:placeholder-shown)]:-translate-y-1.5
                          peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">
                        Type your text...</label>
                </div>

                @error('text')
                <div class="text-red-500 text-sm em p-1">{{ $message }}</div>
                @enderror

                <fieldset
                    class="flex items-center justify-center w-full border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mt-4 bg-white">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Choose Style</legend>

                    <div class="w-full flex justify-center items-center flex-wrap">
                        @foreach(config('text-styler') as $style => $prompt)
                            <button type="button" wire:click="getText('{{$prompt}}')"
                                    class="min-w-40 w-full sm:w-auto justify-center py-2 font-medium px-4 inline-flex items-center gap-x-2 text-sm rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:outline-none focus:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none m-2">
                                {{ ucwords(str_replace('_', ' ', $style)) }}
                            </button>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset
                    x-data="{
                        copied: false,
                        copy () {
                          $clipboard($refs.content.innerText)
                          this.copied = true
                          setTimeout(() => {
                            this.copied = false
                          }, 1000)
                        }
                      }"
                    id="textStylerOutputContainer"
                    class="bg-gray-100 border border-gray-300 px-5 rounded-lg text-base mt-8">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Output</legend>

                    <div x-ref="content" class="py-5" id="textStylerOutput"></div>

                    <div class="flex justify-center border-t border-gray-300 p-5" id="copyButtonContainer">
                        <x-gradient-button @click="copy">
                            <x-icons.copy class="hover:text-gray-600"/>
                            <span x-text="copied ? 'Copied' : 'Copy Output'"></span>
                        </x-gradient-button>
                    </div>
                </fieldset>

            </div>
        </div>

    @endif

    <script>
        (function () {

            const textStylerOutputContainer = document.getElementById('textStylerOutputContainer');
            const outputElement = document.getElementById('textStylerOutput');

            textStylerOutputContainer.style.display = 'none';

            window.addEventListener('DOMContentLoaded', () => {
                window.Livewire.on('getTextStylerAiResponse', () => {
                    outputElement.innerHTML = '';
                    outputElement.style.display = 'block';

                    Livewire.dispatch('showLoading');
                    scrollToBottom();

                    const source = new EventSource("/text-styler/chat");
                    source.addEventListener("update", function (event) {

                        if (event.data === "<END_STREAMING_SSE>") {
                            source.close();
                            console.log("SSE closed");
                            scrollToBottom();
                            Livewire.dispatch('hideLoading');
                            return;
                        }

                        outputElement.innerHTML += JSON.parse(event.data);
                        scrollToBottom();
                    });

                    source.addEventListener("error", function () {
                        source.close();
                        Livewire.dispatch('hideLoading');
                        console.log("SSE closed due to error");
                    });
                });
            });

            function scrollToBottom() {
                window.scrollTo({
                    top: document.body.scrollHeight + 10000,
                    behavior: 'smooth'
                });
            }

            ////////////////////////////////

            function textareaAutoHeight(el, offsetTop = 0) {
                el.style.height = 'auto';
                el.style.height = `${el.scrollHeight + offsetTop}px`;
            }

            (function () {
                const textareas = [
                    '#hs-floating-textarea-gray',
                ];

                textareas.forEach((el) => {
                    const textarea = document.querySelector(el);
                    const overlay = textarea.closest('.hs-overlay');

                    if (overlay) {
                        const {element} = HSOverlay.getInstance(overlay, true);

                        element.on('open', () => textareaAutoHeight(textarea, 3));
                    } else textareaAutoHeight(textarea, 3);

                    textarea.addEventListener('input', () => {
                        textareaAutoHeight(textarea, 3);
                    });
                });
            })();
        })()
    </script>

</div>
