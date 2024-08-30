<div>

    <div class="flex h-screen bg-gray-50 py-10" x-data="{ openDropdown: null }">

        <!-- Sidebar -->
        <aside class="w-48 bg-white border-r border-gray-200">
            <ul class="mt-4 space-y-0.5 bg-white">
                <li class="hover:bg-gray-100">
                    <a href="#"
                       class="flex items-center align-middle p-2 text-sm">
                        <x-icons.folders class="inline size-6 mr-2"/>
                        All Folders ({{ $this->totalNotesCount }})
                    </a>
                </li>

                @foreach($this->folders as $folder)
                    <li class="folder group relative hover:bg-gray-100"
                        wire:key="folder-{{$folder->id}}">
                        <div class="flex justify-between items-center">
                            <a href="#"
                               class="flex items-center w-full align-middle p-2 text-sm {{ $folder->color }}">
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
                             class="absolute right-[4px] bg-white border text-xs border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700 z-10">
                            <ul>
                                <li>
                                    <a href="#"
                                       @click.prevent="startEdit(); openDropdown = null;"
                                       class="block w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
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


</div>
