<div>

    <x-modal id="textNoteModal" maxWidth="sm:max-w-2xl">
        <x-slot name="title">
            <div class="flex gap-x-2">
                {{ $note && $note->exists ? '✏️ Edit Note' : '➕ Add Note'}}
            </div>
        </x-slot>

        <x-slot name="body">

            <x-flash/>

            <div class="mb-4">
                <input placeholder="Title" wire:model="title" type="text"
                       class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"/>
            </div>

            <div class="mb-4">
                  <textarea
                      wire:model="content"
                      rows="5"
                      class="p-4 pb-12 block w-full bg-gray-100 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                      placeholder="Contents..."></textarea>
            </div>

            @if(!isset($folder))
                <div class="mb-4">
                    <select wire:model.change="note_folder_id"
                            class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                        <option value="">Choose Folder</option>

                        @foreach($this->folders as $folderItem)
                            <option value="{{$folderItem->id}}">{{$folderItem->name}}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-4">
                <div class="flex">
                    <input type="checkbox"
                           wire:model.change="hasReminder"
                           class="shrink-0 mt-0.5 cursor-pointer border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                           id="reminder_checkbox">
                    <label for="reminder_checkbox" class="cursor-pointer text-sm text-gray-500 ms-3">
                        Set Reminder
                    </label>
                </div>
            </div>

            @if($hasReminder)
                <div class="mb-4">
                    <input type="datetime-local" wire:model="reminder_datetime"
                           class="py-3 px-4 block w-full bg-gray-100 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"/>
                </div>

                <div class="mb-4">
                    <div class="flex">
                        <input type="checkbox"
                               wire:model.change="isRecurring"
                               class="shrink-0 mt-0.5 cursor-pointer border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                               id="recurring_checkbox">
                        <label for="recurring_checkbox" class="cursor-pointer text-sm text-gray-500 ms-3">
                            Recurring Reminder
                        </label>
                    </div>
                </div>

                @if($isRecurring)
                    <div class="mb-4">
                        <select wire:model.change="recurringFrequency"
                                class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                            <option value="">Select Frequency</option>
                            <option value="hourly">Hourly</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>

                    @if($recurringFrequency)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">
                                Schedule:
                                <span class="text-pink-500">{{ $this->schedulePreview }}</span>
                            </p>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500">
                                <p class="text-sm italic font-bold mb-1">Next Runs:</p>
                                <ul class="list-disc list-inside ml-2">
                                    @foreach ($this->nextRuns as $run)
                                        <li class="text-sm text-pink-500">{{ $run }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif
            @endif

            <div
                class="flex items-center border-t border-gray-200 pt-4 justify-end">
                <x-gradient-button wire:click="saveNote">
                    <x-icons.ok class="size-5"/>
                    Save
                </x-gradient-button>
            </div>

        </x-slot>
    </x-modal>

</div>