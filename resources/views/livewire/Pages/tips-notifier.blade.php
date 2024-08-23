<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    <div class="px-6 py-1">

        @unless(!$this->tips)
            <div class="flex justify-center w-full mb-4">
                <span
                    class="whitespace-nowrap inline-block py-1.5 px-3 rounded-lg border border-gray-200 font-medium bg-gray-100 text-gray-500 text-xs sm:text-sm md:text-base lg:text-base">
                    No tips added, click button below to add a tip.
                    </span>
            </div>
        @endunless

        <div class="flex w-full {{count($this->tips) ? 'justify-end' : 'justify-center'}}">
            <x-gradient-button data-hs-overlay="#tipModal" wire:click="resetForm">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>

                Add Tip
            </x-gradient-button>
        </div>

        @if (count($this->tips))
            <fieldset
                class="items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-3 pt-0 dark:border-neutral-700">
                <legend class="text-sm text-gray-500 dark:text-neutral-300">Saved Tips</legend>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
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
                                Prompt
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                Frequency
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                Cron Expression
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                                Action
                            </th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600">
                        @foreach($this->tips as $tip)
                            <tr wire:key="apikeyrow-{{ $tip->id }}">
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                                    {{ $tip->name }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                                    {{ $tip->apiKey->model_name }} ({{ $tip->apiKey->llm_type }})
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 truncate text-center">
                                    <div class="hs-tooltip [--trigger:hover] [--placement:top] inline-block text-xs">
                                        <span
                                            class="hs-tooltip-toggle cursor-pointer rounded-lg bg-white p-1 border border-gray-300">
                                            💡
                                            <span
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                class="hs-tooltip-content text-wrap p-4 hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[100] max-w-xs w-full bg-white border border-gray-100 text-start rounded-xl shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full"
                                                role="tooltip">
                                                {{ $tip->prompt }}
                                            </span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                                    {{ ucfirst(str_replace('_', ' ', $tip->schedule_type)) }}
                                </td>
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
                                    <code class="font-bold text-pink-500">{{ $tip->schedule_data['cron'] ?? '' }}</code>
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
                                <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 text-center">
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

                                    <x-confirm-dialog call="deleteTip({{$tip->id}})" x-data x-tooltip.raw="Delete"
                                                      class="px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded">
                                        <x-icons.delete class="w-4 h-4 mx-auto"/>
                                    </x-confirm-dialog>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </fieldset>
        @endif

        <x-modal id="tipModal">
            <x-slot name="title">
                <div class="flex gap-x-2">
                    {{ $model && $model->exists ? '✏️ Edit Tip' : '➕ Add Tip'}}
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
                    <select wire:model.change="schedule_type"
                            class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                        <option value="">Choose Frequency</option>
                        <option value="every_minute">Every Minute</option>
                        <option value="every_hour">Every Hour</option>
                        <option value="every_day">Every Day</option>
                        <option value="every_week">Every Week</option>
                        <option value="every_month">Every Month</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                @if ($schedule_type === 'custom')
                    <div class="mb-4">
                        <input type="text" wire:model.live="cronExpression"
                               class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"
                               placeholder="* * * * *">
                        <p class="mt-1 text-xs">
                            Enter a valid cron expression (e.g., <code class="font-bold text-pink-500">*/5 * * *
                                *</code>
                            for every 5
                            minutes).
                            See <a href="https://crontab.guru" target="_blank"
                                   class="text-blue-500 hover:text-blue-700">crontab.guru</a>
                            for more help.
                        </p>
                    </div>
                @endif

                @if ($cronExpression)
                    <div class="mb-4">
                        <p class="text-sm">Schedule Description: <span
                                class="text-pink-500">{{ $this->schedulePreview }}</span>
                        </p>
                    </div>
                @endif

                @if ($schedule_type && $schedule_type !== 'custom')
                    <div class="mb-4">
                        <p class="text-sm italic font-bold mb-1">Next Runs:</p>
                        <ul class="list-disc list-inside ml-2">
                            @foreach ($this->nextRuns as $run)
                                <li class="text-sm text-pink-500">{{ $run }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    @if ($cronExpression)
                        <div class="mb-4">
                            <p class="text-sm italic font-bold mb-1">Next Runs:</p>
                            <ul class="list-disc list-inside ml-2">
                                @foreach ($this->nextRuns as $run)
                                    <li class="text-sm text-pink-500">{{ $run }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                <div
                    class="flex items-center border-t border-gray-300 pt-4 justify-end">
                    <x-gradient-button wire:click="save">
                        <x-icons.ok class="size-5"/>
                        Save
                    </x-gradient-button>
                </div>

            </x-slot>
        </x-modal>

    </div>
</div>
