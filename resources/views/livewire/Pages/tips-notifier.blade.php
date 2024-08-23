<div class="py-20 px-8">
    <livewire:apikeys.api-key-banner/>

    <div class="p-6">

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
                <option value="">Choose Schedule Type</option>
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
