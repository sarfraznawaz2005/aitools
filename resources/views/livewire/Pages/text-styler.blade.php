<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    <div class="flex justify-center items-center w-full">
        <div class="w-full max-w-4xl">
            <div class="relative">
    <textarea
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

            <fieldset class="flex items-center justify-center w-full border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mt-4 bg-white">
                <legend class="text-sm text-gray-500 dark:text-neutral-300">Choose Style</legend>

                <div class="w-full flex justify-center items-center flex-wrap">

                    @foreach(config('text-styler') as $style => $prompt)
                        <button type="button" wire:click="getText('{{ $style }}')" class="min-w-40 w-full sm:w-auto justify-center py-2 font-medium px-4 inline-flex items-center gap-x-2 text-sm rounded-lg border border-transparent bg-blue-100 text-blue-800 hover:bg-blue-200 focus:outline-none focus:bg-blue-200 disabled:opacity-50 disabled:pointer-events-none m-2">
                            {{ ucwords(str_replace('_', ' ', $style)) }}
                        </button>
                    @endforeach

                </div>
            </fieldset>

        </div>
    </div>

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
