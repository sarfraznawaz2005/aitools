<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    <div class="p-2">

        <x-flash/>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
            <thead class="bg-gray-50 dark:bg-neutral-800">
            <tr>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
                    LLM
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-neutral-300">
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
                    Action
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-neutral-700 dark:divide-neutral-600">
            @foreach($this->tips as $tip)
                <tr wire:key="apikeyrow-{{ $tip->id }}">
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300">
                        {{ $tip->apiKey->model_name }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 truncate">
                        {{ $tip->prompt }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 truncate">
                        {{ ucfirst(str_replace('_', ' ', $tip->schedule_type)) }}
                    </td>
                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-neutral-300 truncate">
                        {{ $tip->schedule_data['cron'] ?? 'N/A' }}
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

        <x-flash/>

        <div class="mb-4">
            <select wire:model="apiKey"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
            <textarea placeholder="Enter your prompt..." wire:model="prompt" rows="3"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </div>

        <div class="mb-4">
            <select id="scheduleType" wire:model.change="scheduleType"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Choose Frequency</option>
                <option value="every_minute">Every Minute</option>
                <option value="every_hour">Every Hour</option>
                <option value="every_day">Every Day</option>
                <option value="every_week">Every Week</option>
                <option value="every_month">Every Month</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        @if ($scheduleType === 'custom')
            <div class="mb-4">
                <input type="text" wire:model.live="cronExpression"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="* * * * *">
                <p class="mt-1 text-xs">
                    Enter a valid cron expression (e.g., <code class="font-bold text-pink-500">*/5 * * * *</code>
                    for every 5
                    minutes).
                    See <a href="https://crontab.guru" target="_blank" class="text-blue-500 hover:text-blue-700">crontab.guru</a>
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

        @if ($scheduleType && $scheduleType !== 'custom')
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
            class="flex justify-end items-center gap-x-4 mt-4">
            <x-gradient-button wire:click="save">
                <x-icons.ok class="size-5"/>
                Save
            </x-gradient-button>
        </div>

    </div>
</div>
