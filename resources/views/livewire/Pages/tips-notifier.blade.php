<div class="py-20 px-8">

    <livewire:apikeys.api-key-banner/>

    <div class="max-w-xl mx-auto py-8">
        <x-flash/>

        <form wire:submit.prevent="submit" class="space-y-4">
            <div>
                <label for="tip" class="block text-sm font-medium text-gray-700">
                    Tip
                    <span class="text-gray-400 ml-1" data-tooltip="Enter the tip text here.">[?]</span>
                </label>
                <textarea id="tip" wire:model="tip"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
            </div>

            <div>
                <label for="schedule_type" class="block text-sm font-medium text-gray-700">
                    Schedule Type
                    <span class="text-gray-400 ml-1"
                          data-tooltip="Select how often you want the tip to trigger.">[?]</span>
                </label>

                <select id="schedule_type" wire:model="schedule_type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo 200 focus:ring-opacity-50">
                    <option value="every_minute">Every Minute</option>
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="custom">Custom</option>
                    <option value="recurring">Recurring</option>
                </select>
            </div>

            @if($schedule_type === 'custom')
                <div>
                    <label for="minute" class="block text-sm font-medium text-gray-700">Minute</label>

                    <select id="minute" wire:model="minute"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for ($i = 0; $i < 60; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="hour" class="block text-sm font-medium text-gray-700">Hour</label>
                    <select id="hour" wire:model="hour"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for ($i = 0; $i < 24; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="day_of_week" class="block text-sm font-medium text-gray-700">Day of Week</label>
                    <select id="day_of_week" wire:model="day_of_week"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo 200 focus:ring-opacity-50">
                        @for ($i = 0; $i < 7; $i++)
                            <option
                                value="{{ $i }}">{{ \Carbon\Carbon::createFromFormat('w', $i)->format('l') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>

                    <select id="month" wire:model="month"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for ($i = 1; $i <= 12; $i++)
                            <option
                                value="{{ $i }}">{{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="day_of_month" class="block text-sm font-medium text-gray-700">Day of Month</label>
                    <select id="day_of_month" wire:model="day_of_month"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for ($i = 1; $i <= $daysInMonth; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            @if($schedule_type === 'recurring')
                <div>
                    <label for="recurring_days" class="block text-sm font-medium text-gray-700">Recurring Days</label>
                    <select id="recurring_days" wire:model="recurring_days" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @for ($i = 1; $i <= 31; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            @endif

            <div class="mt-4">
                <p><strong>Schedule Preview:</strong> {{ $preview }}</p>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border
                border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest
                hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300
                disabled:opacity-25 transition ease-in-out duration-150">
                Schedule Tip
            </button>
        </form>
        <div class="mt-8">
            <h2 class="text-lg font-medium text-gray-900">Existing Tips</h2>
            <div class="mt-4 space-y-4">
                <div class="flex items-center">
                    <select wire:model="batch_action" class="mr-2 block w-full rounded-md
                        border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200
                        focus:ring-opacity-50">
                        <option value="">Batch Action</option>
                        <option value="delete">Delete Selected</option>
                        <option value="edit">Edit Selected</option>
                    </select>
                    <button wire:click.prevent="batchDelete" class="inline-flex items-center
                        px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-md hover:bg-red-700
                        focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150" {{$batch_action !== 'delete' ? 'disabled' : '' }}>
                        Apply
                    </button>
                    <button wire:click.prevent="batchEdit" class="ml-2 inline-flex items
                        center px-3 py-1.5 bg-yellow-500 text-white text-xs font-semibold rounded-md hover:bg
                        yellow-700 focus:outline-none focus:ring ring-yellow-300 transition ease-in-out duration
                        150" {{ $batch_action !== 'edit' ? 'disabled' : '' }}>
                        Apply
                    </button>
                </div>

                @foreach($tips as $tip)
                    <div class="p-4 bg-white shadow rounded-md">
                        <div class="flex items-start">
                            <input type="checkbox" wire:model="selected_tips" value="{{ $tip->id }}" class="mr-3 mt-1">
                            <div class="text-sm text-gray-700 flex-grow">
                                <p><strong>Tip:</strong> {{ $tip->tip }}</p>
                                <p><strong>Schedule:</strong> {{ ucfirst(str_replace('_', ' ', $tip->schedule_type)) }}
                                </p>

                                @if($tip->schedule_type === 'custom')
                                    <p><strong>Custom Schedule:</strong>
                                        Minute: {{ $tip->minute ?? '*' }},
                                        Hour: {{ $tip->hour ?? '*' }},
                                        Day of
                                        Week: {{ $tip->day_of_week !== null ? \Carbon\Carbon::createFromFormat('w', $tip->day_of_week)->format('l') : '*' }}
                                        ,
                                        Day of Month: {{ $tip->day_of_month ?? '*' }},
                                        Month: {{ $tip->month !== null ? \Carbon\Carbon::createFromFormat('m', $tip->month)->format('F') : '*' }}
                                    </p>
                                @elseif($tip->schedule_type === 'recurring')
                                    <p><strong>Recurring Schedule:</strong>
                                        Days: {{ implode(', ', $tip->recurring_days ?? []) }},
                                        Time: {{ $tip->hour }}:{{ $tip->minute }}
                                    </p>
                                @endif
                            </div>

                            <div class="text-right">
                                <button wire:click="deleteTip({{ $tip->id }})"
                                        class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring ring-red-300 transition ease-in-out duration-150">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
