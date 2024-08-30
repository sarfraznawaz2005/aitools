<div>

    <div class="flex h-screen bg-gray-50 py-10">

        <!-- Sidebar -->
        <aside class="w-48 bg-white border-r border-gray-200">
            <ul class="mt-4 space-y-0.5 bg-white">
                <li>
                    <a href="#"
                       class="flex items-center align-middle p-2 text-sm hover:bg-gray-300 bg-gray-200">
                        <x-icons.folders class="inline size-6 mr-2" />
                        All Folders ({{ $this->totalNotesCount }})
                    </a>
                </li>

                @foreach($this->folders as $folder)
                    <li>
                        <a href="#"
                           class="flex items-center align-middle p-2 text-sm hover:bg-gray-100 {{ $folder->color }}">
                            <x-icons.folder class="inline size-6 mr-2" />
                            {{ $folder->name }} ({{ $folder->notes->count() }})
                        </a>
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
