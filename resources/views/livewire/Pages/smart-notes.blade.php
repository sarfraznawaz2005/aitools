<div>
    <div class="flex">
        <livewire:notes.sidebar/>

        <main class="flex-1 bg-gray-50 border-l">

            <livewire:apikeys.api-key-banner/>

            <div class="text-center font-medium text-gray-400 text-xl p-2 h-screen items-center flex justify-center">
                <span
                    class="inline-flex items-center gap-x-1.5 py-3 px-6 rounded-full bg-zinc-200 text-gray-500 dark:bg-yellow-800/30 dark:text-yellow-500">
                    You have total of {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders

                    <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="py-2 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <x-icons.plus/>
                        Add Note
                        <svg
                            class="size-4"
                            :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div
                        x-cloak
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 min-w-32 bg-white mt-0.5 z-40 shadow-lg space-y-0.5 divide-y divide-gray-200"
                        role="menu"
                    >
                        <div class="py-2 first:pt-0 last:pb-0">
                            <a
                                wire:click.prevent="$dispatch('openTextNoteModal')"
                                class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                href="#"
                            >
                                <x-icons.text class="shrink-0 size-4"/>
                                Text Note
                            </a>
                            <a
                                @click.prevent="$wire.export('txt')"
                                class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                href="#"
                            >
                                <x-icons.link class="shrink-0 size-4"/>
                                Link Note
                            </a>
                        </div>
                    </div>
                </div>
                </span>
            </div>
        </main>
    </div>

    <livewire:notes.text-note />
    <livewire:notes.link-note />
</div>
