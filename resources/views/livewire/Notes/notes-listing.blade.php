<div>
    <div class="flex bg-white">

        <livewire:notes.sidebar :folder="$folder"/>

        <main class="flex-1 pt-12 border-l {{$folder->getBorderColor()}}">

            <div
                class="flex justify-between items-center w-full shadow-lg sticky top-12 z-40 px-8 p-3 mb-4 border-b {{ $folder->getBackGroundColor() }} {{ $folder->getBorderColor() }}">
                <div class="font-[600] {{ $folder->color }}">
                    {{$folder->name}} ({{$folder->notes->count()}})
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="py-2 px-2 inline-flex items-center rounded-full gap-x-2 text-sm font-medium border border-gray-200 bg-white text-gray-800 shadow hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
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
                        class="absolute right-0 mt-0.5 min-w-32 z-40 shadow-lg space-y-0.5 divide-y divide-gray-200"
                        role="menu"
                    >
                        <div class="py-2 first:pt-0 last:pb-0">
                            <a
                                wire:click.prevent="$dispatch('openTextNoteModal')"
                                class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 bg-white hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                href="#"
                            >
                                <x-icons.text class="shrink-0 size-4"/>
                                Text Note
                            </a>
                            <a
                                @click.prevent="$wire.export('txt')"
                                class="flex items-center gap-x-3.5 py-2 px-3 text-sm text-gray-800 bg-white hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                href="#"
                            >
                                <x-icons.link class="shrink-0 size-4"/>
                                Link Note
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-8 my-4">
                <livewire:apikeys.api-key-banner/>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 mt-8 px-8">

                @foreach($this->notes as $note)
                    <div class="p-4 bg-gray-50 rounded-lg transition-shadow border relative flex flex-col"
                         wire:key="note-{{$note->id}}">

                        <div class="relative min-h-24 max-h-24">

                            <div x-data="{ open: false, subOpen: false, subMenuLeft: false }"
                                 class="absolute top-0 right-0" x-cloak x-init="
                                    $nextTick(() => { open = false; });

                                    $wire.on('updated', () => {
                                        open = false;
                                        subOpen = false;
                                        subMenuLeft = false;
                                    });

                                    Livewire.on('notesUpdated', () => {
                                        open = false;
                                        subOpen = false;
                                        subMenuLeft = false;
                                    });
                                ">

                                <button @click="open = !open" class="text-gray-500 hover:text-gray-700">
                                    <x-icons.dotsv/>
                                </button>

                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    @click.outside="open = false"
                                    class="absolute right-[4px] top-6 z-50 w-32 bg-white text-xs shadow-lg"
                                >
                                    <a href="#" wire:click.prevent="$dispatch('openTextNoteModalEdit', [{{$note->id}}])"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <x-icons.edit class="inline-block mr-2 text-gray-500"/>
                                        Edit
                                    </a>
                                    <div
                                        class="relative"
                                        @mouseenter="subOpen = true; subMenuLeft = (window.innerWidth - $el.getBoundingClientRect().right < 150);"
                                        @mouseleave="subOpen = false"
                                    >
                                        <a href="#" @click.prevent="subOpen = !subOpen"
                                           class="px-4 py-2 text-gray-700 hover:bg-gray-100 flex items-center justify-between">
                                            <span>
                                                <x-icons.share class="inline-block mr-2 text-gray-400"/>
                                                Move
                                            </span>
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                        <div
                                            x-show="subOpen"
                                            :class="subMenuLeft ? 'right-full' : 'left-full'"
                                            class="absolute top-0 z-50 w-full ml-0.5 bg-white shadow-lg"
                                        >
                                            @foreach($this->folders as $folderItem)
                                                @if($folderItem->id !== $folder->id)
                                                    <a wire:key="mvfolder-{{$folderItem->id}}" href="#"
                                                       wire:click.prevent="moveToFolder({{$folderItem->id}}, {{$note->id}})"
                                                       class="block px-4 py-2 font-[500] hover:bg-gray-100 {{$folderItem->color}}">
                                                        {{$folderItem->name}}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    <x-confirm-dialog
                                        call="deleteNote({{$note->id}}); open = false"
                                        class="px-3 py-2 text-left block bg-white hover:bg-gray-100 w-full">
                                        <x-icons.delete class="inline-block mr-2 ml-1 text-red-500"/>
                                        Delete
                                    </x-confirm-dialog>
                                </div>
                            </div>

                            <div class="w-full text-sm">
                                <span class="font-semibold text-gray-700">{{$note->title}}</span>
                                <p class="mt-2 text-gray-600">
                                    {{Str::limit($note->content, 100)}}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


        </main>
    </div>

    <livewire:notes.text-note :folder="$folder"/>
    <livewire:notes.link-note :folder="$folder"/>

</div>
