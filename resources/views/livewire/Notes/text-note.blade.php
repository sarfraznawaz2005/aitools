<div>

    <x-modal id="addCustomNoteModal">
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
                      rows="3"
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
