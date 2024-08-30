@php($tools = config('tools'))

<div>

    <div class="flex h-screen bg-gray-50 py-10" x-data="{ openDropdown: null }">

        <!-- Sidebar -->
        <aside class="w-48 bg-white border-r border-gray-200">
            <ul class="mt-4 space-y-0.5 bg-white">

                <li class="mx-2 mb-2">
                    <x-gradient-link class="w-full" href="#" wire:click.prevent="addFolder()">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"/>
                            <path d="M12 5v14"/>
                        </svg>
                        New Folder
                    </x-gradient-link>
                </li>

                @foreach($this->folders as $folder)
                    <li class="folder group relative hover:bg-gray-100"
                        wire:key="folder-{{$folder->id}}">
                        <div class="flex justify-between items-center">
                            <a
                                wire:navigate
                                href="{{route($tools['smart-notes']['route'] . '.openfolder', $folder->id)}}"
                                class="flex items-center w-full align-middle p-2 text-sm truncate {{ $folder->color }}">
                                <x-icons.folder class="inline size-6 mr-2"/>
                                {{ $folder->name }} ({{ $folder->notes->count() }})
                            </a>
                            <div>
                                <button
                                    @click.prevent.stop="openDropdown = (openDropdown === {{$folder->id}}) ? null : {{$folder->id}}"
                                    class="ml-auto cursor-pointer hidden group-hover:inline-block pr-2">
                                    <x-icons.dots class="inline-block"/>
                                </button>
                            </div>
                        </div>

                        <div x-show="openDropdown === {{$folder->id}}"
                             @click.away="openDropdown = null"
                             class="absolute right-[4px] bg-white border text-xs border-gray-200 rounded-lg shadow-lg z-10">
                            <ul>
                                <li>
                                    <a href="#"
                                       wire:click.prevent="editFolder({{$folder->id}})"
                                       @click.prevent="openDropdown = null;"
                                       class="block w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <x-icons.edit class="inline-block mr-2 text-gray-500"/>
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <x-confirm-dialog call="deleteFolder({{$folder->id}})"
                                                      title="Delete"
                                                      text="Are you sure you want to delete? This will delete all notes in this folder!"
                                                      class="px-3 py-2 text-left block text-sm bg-white hover:bg-gray-100 w-full">
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

        <!-- Main Content -->
        <main class="flex-1 p-6">

            <livewire:apikeys.api-key-banner/>

            <div class="text-center font-medium text-gray-400 text-2xl p-2 h-screen items-center flex justify-center">
                <span class="inline-flex items-center gap-x-1.5 py-3 px-6 rounded-full bg-gray-100 text-gray-500 dark:bg-yellow-800/30 dark:text-yellow-500">
                    You have total of {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Note Card -->
                <div class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Note Title 1</h2>
                    <p class="mt-2 text-gray-600">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla accumsan...
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Read more</a>
                    </div>
                </div>

                <!-- Another Note Card -->
                <div class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Note Title 2</h2>
                    <p class="mt-2 text-gray-600">
                        Suspendisse potenti. Nullam auctor, urna eget imperdiet lobortis...
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Read more</a>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Note Title 1</h2>
                    <p class="mt-2 text-gray-600">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla accumsan...
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Read more</a>
                    </div>
                </div>

                <!-- Another Note Card -->
                <div class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <h2 class="text-lg font-semibold text-gray-700">Note Title 2</h2>
                    <p class="mt-2 text-gray-600">
                        Suspendisse potenti. Nullam auctor, urna eget imperdiet lobortis...
                    </p>
                    <div class="mt-4">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Read more</a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <x-modal id="notesFolderModal">
        <x-slot name="title">
            <div class="flex gap-x-2">
                {{ $model && $model->exists ? '✏️ Edit Folder' : '➕ Add Folder'}}
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
