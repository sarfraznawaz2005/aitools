<div x-data="{ llm_type: '' }">

    <x-flash/>

    <form wire:submit.prevent="save">
        @csrf

        <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700">
            <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">ADD API KEY</legend>

            <!-- Select -->
            <select x-model="llm_type" wire:model="llm_type" id="llm_type"
                    class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value="">Choose LLM</option>
                <option value="openai">OpenAi</option>
                <option value="gemini">Gemini</option>
                <option value="ollama">Ollama</option>
            </select>
            <!-- End Select -->

            <br>

            <!-- Floating Input -->
            <div class="relative" x-show="llm_type === 'ollama'">
                <input type="url" wire:model="base_url" id="base_url"
                       class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
                <label for="base_url"
                       class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">Base
                    URL</label>
                <br>
            </div>
            <!-- End Floating Input -->

            <!-- Floating Input -->
            <div class="relative">
                <input type="text" wire:model="api_key" id="api_key"
                       class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
                <label for="api_key"
                       class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">API
                    Key</label>
            </div>
            <!-- End Floating Input -->

            <br>

            <!-- Floating Input -->
            <div class="relative">
                <input type="text" wire:model="name" id="name"
                       class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
                <label for="name"
                       class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">Model
                    Name</label>
                <p class="mt-2 text-sm text-gray-400 dark:text-neutral-500">&nbsp;Based on your selected LLM</p>
            </div>
            <!-- End Floating Input -->

            <div
                class="flex justify-end items-center gap-x-4 pt-4 border-t-2 border-gray-100 dark:border-neutral-700 mt-4">
                <button type="button"
                        class="py-2 px-3 inline-flex items-center text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                        data-hs-overlay="#general-modal">
                    Close
                </button>
                <button type="submit"
                        class="gap-x-1 py-2 px-3 inline-flex items-center text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    <x-icons.ok class="size-5"/>
                    Save
                </button>
            </div>
        </fieldset>
    </form>
</div>
