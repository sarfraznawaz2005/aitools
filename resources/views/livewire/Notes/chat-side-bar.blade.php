<div>
    <div x-data="{ open: false }">
        <button type="button" @click="open = true"
                class="py-2 px-4 mr-2 inline-flex items-center gap-x-1 text-sm font-medium rounded-lg border-transparent bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
            <x-icons.chat class="size-4"/>
            Chat With Your Notes
        </button>

        <div x-show="open" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full"
             x-transition:enter-end="transform translate-x-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform translate-x-0"
             x-transition:leave-end="transform translate-x-full"
             class="fixed inset-0 z-50 pt-12 flex justify-end">
            <div
                @click.away="open = false"
                class="relative w-full max-w-md h-full bg-gray-50 shadow-2xl flex flex-col">
                <!-- Close Button -->
                <button @click="open = false" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Sidebar Content -->
                <div class="flex-1 p-4 overflow-y-auto">
                    <h2 class="text-lg font-semibold mb-4">Chat with Your Notes</h2>
                    <!-- Your chat content goes here -->
                </div>

                <!-- Chat Input at the Bottom -->
                <div class="p-2 flex flex-col sm:flex-row bg-white items-center border border-gray-300 rounded-lg m-3 mx-4">
                    <div>
                        <button @click="open = !open" class="text-gray-500 hover:text-gray-700 mt-1">
                            <x-icons.dotsv/>
                        </button>
                    </div>
                    <div class="relative w-full">
                        <input type="url" autofocus autocomplete="off"
                               class="py-1 block w-full border-transparent rounded-lg text-sm focus:border-transparent focus:ring-transparent disabled:opacity-50 disabled:pointer-events-none"
                               placeholder="Ask me anything...">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
