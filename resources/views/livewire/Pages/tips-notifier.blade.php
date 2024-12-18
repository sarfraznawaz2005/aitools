<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    @if (hasApiKeysCreated())
        <div class="px-6 py-1">

            @if (!count($this->tips))
                <div class="flex justify-center w-full mb-4">
                <span
                    class="inline-block py-1.5 px-3 rounded-lg border border-gray-200 font-medium bg-gray-100 text-gray-500 text-xs sm:text-sm md:text-base lg:text-base">
                    💡 Get regular AI tips by setting a prompt and schedule. The AI will send you notifications based on your prompt at the specified times.
                </span>
                </div>

                <div class="flex justify-center w-full mb-4">
                <span
                    class="inline-block py-1.5 px-3 rounded-lg border border-gray-200 font-medium bg-gray-100 text-gray-500 text-xs sm:text-sm md:text-base lg:text-base">
                    No tips added, click button below to add a tip.
                </span>
                </div>
            @endif

            <fieldset class="border border-gray-200 rounded-lg p-4 bg-white mb-4">
                <legend class="text-sm font-bold text-gray-500 dark:text-neutral-300">Tips Schedules</legend>
                <div class="flex w-full {{count($this->tips) ? '' : 'justify-center'}}">
                    <x-gradient-button data-hs-overlay="#tipModal" wire:click="resetForm">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="M12 5v14"/>
                        </svg>

                        Add Schedule
                    </x-gradient-button>
                </div>

                @if (count($this->tips))
                    <div class="items-center justify-center font-semibold w-full border border-gray-200 mt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-100 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                        Name
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                        LLM
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                        Frequency
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 w-fit text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                        Action
                                    </th>
                                </tr>
                                </thead>

                                <tbody
                                    class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600 font-medium">
                                @foreach($this->tips as $tip)
                                    <tr wire:key="apikeyrow-{{ $tip->id }}">
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                            {{ $tip->name }}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                            {{ $tip->apiKey->model_name }} ({{ $tip->apiKey->llm_type }})
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                                            {{Lorisleiva\CronTranslator\CronTranslator::translate($tip->cron)}}
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap text-sm text-center">
                                            @if ($tip->active)
                                                <span
                                                    class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">
                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-2 whitespace-nowrap w-0 text-sm text-gray-500 dark:text-neutral-300 text-center">
                                            <button x-data x-tooltip.raw="Toggle Status"
                                                    wire:click="toggleStatus({{ $tip->id }})"
                                                    class="items-center px-2 py-1 text-white rounded mr-2 {{$tip->active ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-800'}}">
                                                <x-icons.ok class="w-4 h-4 mx-auto"/>
                                            </button>

                                            <button
                                                x-data x-tooltip.raw="Edit"
                                                wire:click="edit({{ $tip->id }})"
                                                class="items-center px-2 py-1 text-white bg-blue-600 hover:bg-blue-800 rounded mr-2">
                                                <x-icons.edit class="w-4 h-4 mx-auto"/>
                                            </button>

                                            <x-confirm-dialog call="deleteTip({{$tip->id}})" x-data
                                                              x-tooltip.raw="Delete"
                                                              class="px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded">
                                                <x-icons.delete class="w-4 h-4 mx-auto"/>
                                            </x-confirm-dialog>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </fieldset>

            <fieldset class="border border-gray-200 rounded-lg p-4 bg-white mb-4 mt-8">
                <legend class="text-sm text-gray-500 dark:text-neutral-300 font-bold">AI Generated Tips
                    ({{ App\Models\TipContent::query()->count() }})
                    <span class="cursor-pointer" x-data x-tooltip.raw="Refresh" wire:click="$refresh">🔄</span>
                </legend>

                <!-- Add search field -->
                <div class="relative bg-transparent">

                    <div>
                        <x-icons.search class="absolute top-3 left-4 text-gray-500"/>
                    </div>
                    <div class="mb-4">
                        <input type="text" wire:model.live.debounce.500ms="searchQuery"
                               placeholder="Search content..."
                               class="py-3 pl-12 pr-6 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"/>
                    </div>
                </div>

                @if($this->contents->count())
                    <div class="items-center justify-center font-semibold w-full border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead class="bg-gray-100 dark:bg-neutral-800">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                    Source
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                    Title
                                </th>
                                <th scope="col"
                                    x-data x-tooltip.raw="sort"
                                    wire:click.prevent="sortBy('id')"
                                    class="px-6 py-3 text-center cursor-pointer text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                    Date
                                </th>
                                <th scope="col"
                                    x-data x-tooltip.raw="sort"
                                    wire:click.prevent="sortBy('favorite')"
                                    class="px-6 py-3 text-center cursor-pointer text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                    Favorite
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 w-fit text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                    Action
                                </th>
                            </tr>
                            </thead>

                            <tbody
                                class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600 font-medium">
                            @foreach($this->contents as $content)
                                <tr wire:key="apikeyrow-{{ $content->id }}">
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                        {{ $content->tip->name }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 cursor-pointer"
                                        wire:click="viewContents({{ $content->id }})"
                                        x-data x-tooltip.raw="click to view">
                                        {{ Str::limit($content->title, 75) }}
                                    </td>
                                    <td
                                        class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center"
                                        x-data x-tooltip.raw="View Content"
                                    >
                                        ️‍{{ $content->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-center">
                                        @if ($content->favorite)
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500">
                                            Yes
                                        </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">
                                            No
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-2 whitespace-nowrap w-0 text-sm text-gray-500 dark:text-neutral-300 text-center">
                                        <button x-data x-tooltip.raw="Toggle Favorite Status"
                                                wire:click="toggleContentFavoriteStatus({{ $content->id }})"
                                                class="items-center px-2 py-1 text-white rounded mr-2 {{$content->favorite ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-600 hover:bg-gray-800'}}">
                                            <x-icons.ok class="w-4 h-4 mx-auto"/>
                                        </button>

                                        <x-confirm-dialog call="deleteContent({{$content->id}})" x-data
                                                          x-tooltip.raw="Delete"
                                                          class="px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded">
                                            <x-icons.delete class="w-4 h-4 mx-auto"/>
                                        </x-confirm-dialog>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $this->contents->links() }} <!-- Single Pagination control for contents -->
                    </div>
                @endif

            </fieldset>


            <x-modal id="tipModal">
                <x-slot name="title">
                    <div class="flex gap-x-2">
                        {{ $model && $model->exists ? '✏️ Edit Tip Schedule' : '➕ Add Tip Schedule'}}
                    </div>
                </x-slot>

                <x-slot name="body">

                    <x-flash/>

                    <div class="mb-4">
                        <select wire:model="api_key_id"
                                class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                            <option value="">Choose LLM</option>

                            @foreach($this->apiKeys->groupBy('llm_type') as $llmType => $groupedApiKeys)
                                <optgroup label="{{ $llmType }}">
                                    @foreach($groupedApiKeys as $apiKey)
                                        <option
                                            value="{{ $apiKey->id }}">{{ $apiKey->model_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <input placeholder="Name" wire:model="name" type="text"
                               class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"/>
                    </div>

                    <div class="mb-4">
                        <textarea placeholder="Enter your prompt..." wire:model="prompt" rows="3"
                                  class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"></textarea>
                    </div>

                    <div class="mb-4">
                        <select wire:model.change="cron"
                                class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                            <option value="">Choose Frequency</option>
                            <option value="* * * * *">Every Minute</option>
                            <option value="0 * * * *">Every Hour</option>
                            <option value="0 0 * * *">Every Day</option>
                            <option value="0 0 * * 0">Every Week</option>
                            <option value="0 0 1 * *">Every Month</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <input type="text" wire:model.live="cron"
                               placeholder="Type cron expression or select above"
                               class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">

                        <p class="mt-1 text-xs">
                            Enter a valid cron expression
                            (e.g., <code class="font-bold text-pink-500">*/5 * * * *</code> for every 5 minutes).
                            See <a href="https://crontab.guru" target="_blank"
                                   class="text-blue-500 hover:text-blue-700">crontab.guru</a> for more help.
                        </p>
                    </div>

                    @if (!empty($cron))
                        <div class="mb-4">
                            <p class="text-sm">Schedule: <span
                                    class="text-pink-500">{{ $this->schedulePreview }}</span>
                            </p>
                        </div>
                    @endif

                    @if ($this->nextRuns)
                        <div class="mb-4">
                            <p class="text-sm italic font-bold mb-1">Next Runs:</p>
                            <ul class="list-disc list-inside ml-2">
                                @foreach ($this->nextRuns as $run)
                                    <li class="text-sm text-pink-500">{{ $run }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div
                        class="flex items-center border-t border-gray-200 pt-4 justify-end">
                        <x-gradient-button wire:click="save">
                            <x-icons.ok class="size-5"/>
                            Save
                        </x-gradient-button>
                    </div>

                </x-slot>
            </x-modal>

        </div>
    @endif
</div>
