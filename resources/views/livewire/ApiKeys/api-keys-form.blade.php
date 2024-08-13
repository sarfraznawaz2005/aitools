<div>

    @if ($apiKeys && count($apiKeys))
        <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mb-4">
            <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">SAVED API KEYS</legend>

            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gray-50 dark:bg-neutral-800">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                        Model Name
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                        Type
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600">
                @foreach($apiKeys as $apiKey)
                    <tr wire:key="{{ $apiKey->id }}">
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                            {{ $apiKey->model_name }}
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                            {{ $apiKey->llm_type }}
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                            @if ($apiKey->active)
                                <button x-data x-tooltip.raw="This is currently default"
                                        class="cursor-default items-center px-2 py-1 text-white bg-green-600 rounded mr-2">
                                    <x-icons.ok class="w-4 h-4 mx-auto"/>
                                </button>
                            @else
                                <button x-data x-tooltip.raw="Make Default"
                                        wire:click="markDefault({{ $apiKey->id }})"
                                        class="items-center px-2 py-1 text-white bg-gray-600 hover:bg-gray-800 rounded mr-2">
                                    <x-icons.ok class="w-4 h-4 mx-auto"/>
                                </button>
                            @endif

                            <button
                                x-data x-tooltip.raw="Edit"
                                wire:click="edit({{ $apiKey->id }})"
                                class="items-center px-2 py-1 text-white bg-blue-600 hover:bg-blue-800 rounded mr-2">
                                <x-icons.edit class="w-4 h-4 mx-auto"/>
                            </button>

                            <x-confirm-dialog :id="$apiKey->id" using="deleteApiKey" x-data x-tooltip.raw="Delete"
                                              class="px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded">
                                <x-icons.delete class="w-4 h-4 mx-auto"/>
                            </x-confirm-dialog>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </fieldset>
    @endif

    <x-flash/>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            {{ $model->exists ? 'EDIT API KEY' : 'ADD API KEY'}}
        </legend>

        @if ($model->exists)
            <button wire:click="resetForm"
                    class="flex justify-center items-center py-1 px-4 mb-8 inline-block text-gray-600 bg-red-100 rounded hover:bg-red-200">
                <x-icons.close class="size-5 mr-2 inline-block"/>
                Cancel
            </button>
        @endif

        <!-- Select -->
        <div class="relative mb-3">
            <select wire:model="llm_type" id="llm_type"
                    class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value="">Choose LLM</option>
                <option value="OpenAI">OpenAI</option>
                <option value="Gemini">Gemini</option>
                <option value="Ollama">Ollama</option>
            </select>
        </div>
        <!-- End Select -->

        <!-- Floating Input -->
        <div class="relative mb-3" x-show="$wire.llm_type === 'Ollama'">
            <input type="url" wire:model="base_url"
                   id="base_url"
                   placeholder="http://127.0.0.1:11434"
                   class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
            <label for="base_url"
                   class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">Base
                URL</label>
        </div>
        <!-- End Floating Input -->

        <!-- Floating Input -->
        <div class="relative mb-3">
            <input type="text" wire:model="api_key" id="api_key"
                   class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
            <label for="api_key"
                   class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">API
                Key</label>
        </div>
        <!-- End Floating Input -->

        <!-- Floating Input -->
        <div class="relative">
            <input type="text" wire:model="model_name" id="model_name"
                   class="peer p-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2">
            <label for="model_name"
                   class="absolute top-0 start-0 p-4 h-full text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] dark:text-neutral-400 peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-1.5 peer-focus:text-gray-500 dark:peer-focus:text-neutral-500 peer-[:not(:placeholder-shown)]:scale-90 peer-[:not(:placeholder-shown)]:translate-x-0.5 peer-[:not(:placeholder-shown)]:-translate-y-1.5 peer-[:not(:placeholder-shown)]:text-gray-500 dark:peer-[:not(:placeholder-shown)]:text-neutral-500">Model
                Name</label>
            <p class="mt-2 text-sm text-gray-400 dark:text-neutral-500">&nbsp;Based on your selected LLM</p>
        </div>
        <!-- End Floating Input -->

        <div
            class="flex justify-end items-center gap-x-4 mt-4">
            <x-gradient-button wire:click="save">
                <x-icons.ok class="size-5"/>
                Save
            </x-gradient-button>
        </div>
    </fieldset>

</div>
