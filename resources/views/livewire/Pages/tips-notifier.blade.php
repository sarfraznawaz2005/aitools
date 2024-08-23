<div class="py-20 px-8">

    <livewire:apikeys.api-key-banner/>

    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-4">Tip Manager</h2>

        <form wire:submit.prevent="saveTip" class="mb-8">
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700">Tip Content</label>
                <textarea id="content" wire:model="content" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>

            <div class="mb-4">
                <label for="scheduleType" class="block text-sm font-medium text-gray-700">Schedule Type</label>
                <select id="scheduleType" wire:model.change="scheduleType"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="every_minute">Every Minute</option>
                    <option value="every_hour">Every Hour</option>
                    <option value="every_day">Every Day</option>
                    <option value="every_week">Every Week</option>
                    <option value="every_month">Every Month</option>
                    <option value="custom">Custom</option>
                </select>
            </div>

            @if ($scheduleType === 'custom')
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach (['minute', 'hour', 'day', 'month', 'weekday'] as $field)
                        <div>
                            <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">{{ ucfirst($field) }}</label>
                            <input type="text" id="{{ $field }}" wire:model="scheduleData.{{ $field }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   placeholder="*">
                            <p class="mt-1 text-xs text-gray-500">{{ $customFieldHints[$field] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700">Schedule Preview: {{ $this->schedulePreview }}</p>
            </div>

            <div class="mb-4">
                <p class="text-sm font-medium text-gray-700">Next Possible Runs:</p>
                <ul class="list-disc list-inside">
                    @foreach ($this->nextRuns as $run)
                        <li class="text-sm text-gray-600">{{ $run }}</li>
                    @endforeach
                </ul>
            </div>

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Save Tip
            </button>
        </form>

        <h3 class="text-xl font-semibold mb-2">Saved Tips</h3>
        <ul class="space-y-4">
            @foreach ($tips as $tip)
                <li class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <p class="text-sm font-medium text-gray-900">{{ $tip->content }}</p>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Schedule: {{ json_encode($tip->schedule_data) }}</p>
                        <button wire:click="deleteTip({{ $tip->id }})"
                                class="mt-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
