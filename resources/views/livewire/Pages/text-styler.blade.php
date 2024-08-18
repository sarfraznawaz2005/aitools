<div class="mt-20 px-8">

    {{--<x-flash/>--}}

    <livewire:apikeys.api-key-banner/>

    @if (hasApiKeysCreated())

        <div class="fixed left-3 bottom-3 inline-flex">
            <livewire:general.model-selector for="{{App\Constants::TEXTSTYLER_SELECTED_LLM_KEY}}"/>
        </div>

        <div class="flex justify-center items-center w-full">
            <div class="w-full max-w-4xl">

               <textarea
                   class="py-3 px-4 block w-full border-gray-200 rounded-lg text-base focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                   wire:model="text"
                   rows="8"
                   placeholder="Type your text...">
               </textarea>
                @error('text')
                <div class="text-red-500 text-sm em p-1">{{ $message }}</div>
                @enderror

                <fieldset
                    class="flex items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-5 pb-7 dark:border-neutral-700 mt-4 bg-white">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Choose Style</legend>

                    <div class="w-full flex justify-center items-center flex-wrap">
                        @foreach(config('text-styler') as $key => $style)
                            <button type="button" wire:click="getText('{{$style['prompt']}}')"
                                    class="min-w-48 w-full sm:w-auto justify-center py-2 font-medium px-4 inline-flex items-center m-2 text-sm rounded-lg border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none">

                                {{ $style['icon'] . ' ' .ucwords(str_replace('_', ' ', $key)) }}
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
                    class="bg-gray-100 border border-gray-300 px-5 rounded-lg text-base font-semibold my-4 invisible">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Output</legend>

                    <div x-ref="content" class="py-5 font-medium" id="textStylerOutput"></div>

                    <div class="justify-center border-t border-gray-300 p-5 hidden" id="copyButtonContainer">
                        <x-gradient-button @click="copy">
                            <x-icons.copy/>
                            <span x-text="copied ? 'Copied' : 'Copy Output'"></span>
                        </x-gradient-button>
                    </div>
                </fieldset>

            </div>
        </div>

    @endif

    <script data-navigate-once>
        (function () {

            document.addEventListener('livewire:navigated', () => {
                window.Livewire.on('getTextStylerAiResponse', () => {
                    const textStylerOutputContainer = document.getElementById('textStylerOutputContainer');
                    const copyButtonContainer = document.getElementById('copyButtonContainer');
                    const outputElement = document.getElementById('textStylerOutput');

                    outputElement.style.display = 'block';
                    outputElement.innerHTML = '';

                    scrollToBottom();
                    Livewire.dispatch('showLoading');

                    const source = new EventSource("/text-styler/chat");
                    source.addEventListener("update", function (event) {

                        textStylerOutputContainer.style.visibility = 'visible';

                        if (event.data === "<END_STREAMING_SSE>") {
                            source.close();
                            console.log("SSE closed");
                            Livewire.dispatch('hideLoading');
                            copyButtonContainer.style.display = 'flex';
                            scrollToBottom();
                            return;
                        }

                        outputElement.innerHTML += JSON.parse(event.data);
                        copyButtonContainer.style.display = 'none';
                        scrollToBottom();
                    });

                    source.addEventListener("error", function () {
                        source.close();
                        Livewire.dispatch('hideLoading');
                        console.log("SSE closed due to error");
                    });
                });

            }, {once: true});

            function scrollToBottom() {
                window.scrollTo({
                    top: document.body.scrollHeight + 10000,
                    behavior: 'smooth'
                });
            }

        })()
    </script>

</div>
