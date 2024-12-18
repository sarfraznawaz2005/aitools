@php($tools = config('tools'))

<div>
    <aside class="w-full sm:w-52 bg-white sm:sticky top-0 h-screen min-h-screen sm:h-auto sm:pt-16 pt-4" x-data="{ openDropdown: false }" x-init="
            $nextTick(() => { openDropdown = false; })

            $wire.on('updated', () => {
                openDropdown = false
            });"
    >
        <ul class="space-y-0.5 bg-white">

            <li class="mx-2">
                <button type="button" wire:click.prevent="addFolder()"
                        class="py-2 px-2 w-full bg-gradient-to-r bg-gray-200 hover:bg-gray-300 inline-flex items-center gap-x-1 justify-center text-sm font-medium rounded-lg border border-transparent text-gray-800 focus:outline-none focus:bg-gray-200">
                    <x-icons.plus/>
                    Add Folder
                </button>
            </li>

            @foreach($this->folders as $folderItem)
                <li class="folder group relative hover:{{$folderItem->getBackgroundColor()}}"
                    wire:key="foldersidebar-{{$folderItem->id}}">
                    <div class="flex justify-between items-center">
                        <a
                            wire:navigate
                            href="{{route($tools['smart-notes']['route'] . '.listing', $folderItem->id)}}"
                            class="items-center font-[500] p-2 text-sm w-full sm:w-52 overflow-hidden truncate whitespace-nowrap text-ellipsis
                            {{ $folderItem->color }} {{isset($folder) && $folder->exists && $folderItem->id === $folder->id ? $folder->getBackgroundColor() : ''}}">
                            <x-icons.folder class="inline size-6 mr-2"/>
                            {{ $folderItem->name }} ({{ $folderItem->notes->count() }})
                        </a>
                        <div>
                            <button
                                @click.prevent.stop="openDropdown = (openDropdown === {{$folderItem->id}}) ? null : {{$folderItem->id}}"
                                class="ml-auto cursor-pointer hidden group-hover:inline-block pr-2">
                                <x-icons.dots class="inline-block"/>
                            </button>
                        </div>
                    </div>

                    <div x-show="openDropdown === {{$folderItem->id}}"
                         @click.away="openDropdown = false"
                         x-cloak
                         class="absolute right-[4px] top-7 bg-white w-32 border text-xs border-gray-200 shadow-lg z-10">
                        <ul>
                            <li>
                                <a href="#"
                                   wire:click.prevent="editFolder({{$folderItem->id}})"
                                   @click.prevent="openDropdown = false;"
                                   class="block w-full px-3 py-2 text-gray-700 hover:bg-gray-100">
                                    <x-icons.edit class="inline-block mr-2 text-gray-500"/>
                                    Edit
                                </a>
                            </li>
                            <li>
                                <x-confirm-dialog call="deleteFolder({{$folderItem->id}})"
                                                  text="Are you sure you want to delete? This will delete all notes in this folder!"
                                                  class="px-3 py-2 text-left block bg-white hover:bg-gray-100 w-full">
                                    <x-icons.delete class="inline-block mr-2 text-red-500"/>
                                    Delete
                                </x-confirm-dialog>
                            </li>
                        </ul>
                    </div>
                </li>
            @endforeach
        </ul>
    </aside>

    <x-modal id="notesFolderModal">
        <x-slot name="title">
            <div class="flex gap-x-2">
                {{ $folderItem && $folderItem->exists ? '✏️ Edit Folder' : '➕ Add Folder'}}
            </div>
        </x-slot>

        <x-slot name="body">

            <x-flash/>

            <div class="mb-4">
                <input placeholder="Name" wire:model="name" type="text"
                       class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50"/>
            </div>

            <div class="mb-4">
                <select wire:model.change="color"
                        class="py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50">
                    <option value="">Choose Color</option>
                    <option value="text-gray-600" class="text-gray-600">Gray</option>
                    <option value="text-blue-600" class="text-blue-600">Blue</option>
                    <option value="text-cyan-600" class="text-cyan-600">Cyan</option>
                    <option value="text-green-600" class="text-green-600">Green</option>
                    <option value="text-purple-600" class="text-purple-600">Purple</option>
                    <option value="text-indigo-600" class="text-indigo-600">Indigo</option>
                    <option value="text-red-600" class="text-red-600">Red</option>
                    <option value="text-yellow-600" class="text-yellow-600">Yellow</option>
                    <option value="text-orange-600" class="text-orange-600">Orange</option>
                </select>
            </div>

            <div
                class="flex items-center border-t border-gray-200 pt-4 justify-end">
                <x-gradient-button wire:click="saveFolder">
                    <x-icons.ok class="size-5"/>
                    Save
                </x-gradient-button>
            </div>

        </x-slot>
    </x-modal>

</div>
