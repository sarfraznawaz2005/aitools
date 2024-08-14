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
                            <button type="button" wire:click="getText('{{ $style }}')"
                                    class="min-w-40 w-full sm:w-auto justify-center py-2 font-medium px-4 inline-flex items-center gap-x-2 text-sm rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:outline-none focus:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none m-2">
                                {{ ucwords(str_replace('_', ' ', $style)) }}
                            </button>
                        @endforeach

                        {{$output}}

                    </div>
                </fieldset>

            </div>
        </div>

        <div x-data="{ open: @entangle('showModal') }" x-init="$nextTick(() => { open = true })">
            <div id="hs-task-created-alert"
                 x-cloak
                 class="fixed inset-0 z-[80] overflow-y-auto"
                 x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 role="dialog"
                 tabindex="-1"
                 aria-labelledby="hs-task-created-alert-label">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="relative w-full max-w-lg bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                        <div class="absolute top-2 right-2">
                            <button type="button"
                                    class="inline-flex justify-center items-center w-8 h-8 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:ring-neutral-600"
                                    aria-label="Close"
                                    @click="open = false">
                                <span class="sr-only">Close</span>
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal content -->
                        <div class="p-4 text-center">
                            <!-- Add your modal content here -->
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="hs-task-created-alert-label">
                                Welcome Message
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                This is a welcome message for your application.
                            </p>

                            <div class="flex justify-end mt-6 gap-x-4">
                                <div class="">
                                    <button type="button"
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-green-500 text-white shadow-sm hover:bg-green-600 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                    >
                                        Copy
                                    </button>
                                </div>
                                <div class="flex justify-center gap-x-4">
                                    <button type="button"
                                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                            data-hs-overlay="#hs-task-created-alert">
                                        Close
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif

    <script>
        (function () {

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
