<div>
    <div class="flex bg-white">

        <livewire:notes.sidebar :folder="$folder"/>

        <main class="flex-1 bg-gray-50 pt-20 px-8 border-l {{$folder->getBorderColor()}}">

            <livewire:apikeys.api-key-banner/>

            <div class="flex justify-between w-full border p-3 rounded-lg mb-4 {{ $folder->getBackGroundColor() }} {{ $folder->getBorderColor() }}">
                <div class="font-bold {{ $folder->color }}">
                    {{$folder->name}} ({{$folder->notes->count()}})
                </div>
                <div>2</div>
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

</div>
