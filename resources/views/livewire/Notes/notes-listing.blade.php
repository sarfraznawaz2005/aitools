<div>

    <div class="flex bg-white">

        <livewire:notes.sidebar :folder="$folder"/>

        <main class="flex-1 pt-12 pb-12 border-l {{$folder->getBorderColor()}}">

            <div
                style="top: 50px;"
                class="flex justify-between items-center w-full shadow-lg sticky z-10 py-2 px-4 mb-4 {{ $folder->getBackGroundColor() }} {{ $folder->getBorderColor() }}">

                <div class="flex items-center justify-between">

                    <div x-data="{ open: false }" class="relative pt-1 mr-4">
                        <button @click="open = !open" x-data x-tooltip.raw="Sort">
                            <x-icons.sort class="cursor-pointer"/>
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
                            class="absolute left-0 mt-0.5 min-w-28 bg-white shadow-lg text-xs space-y-0.5 divide-y divide-gray-200"
                            role="menu"
                        >
                            <div class="py-2 first:pt-0 last:pb-0">
                                <a
                                    wire:click.prevent="sortBy('id')"
                                    class="flex items-center gap-x-3.5 py-2 px-3 text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                    href="#"
                                >
                                    Date
                                    @if($sortField === 'id')
                                        @if($sortAsc)
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="inline-block w-4 h-4 ml-2 text-gray-500" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="inline-block w-4 h-4 ml-2 text-gray-500" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </a>
                                <a
                                    wire:click.prevent="sortBy('title')"
                                    class="flex items-center gap-x-3.5 py-2 px-3 text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                                    href="#"
                                >
                                    Title
                                    @if($sortField === 'title')
                                        @if($sortAsc)
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="inline-block w-4 h-4 ml-2 text-gray-500" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 15l7-7 7 7"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="inline-block w-4 h-4 ml-2 text-gray-500" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        @endif
                                    @endif
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="font-[600] mr-4 uppercase">
                        {{$folder->name}} ({{$folder->notes->count()}})
                    </div>

                    <div>
                        <x-gradient-button class="w-full" href="#" wire:click.prevent="$dispatch('openTextNoteModal')">
                            <x-icons.plus/>
                            Add Note
                        </x-gradient-button>
                    </div>
                </div>

                <div>
                    <input type="text" wire:model.live.debounce.500ms="searchQuery"
                           placeholder="Search Content..."
                           class="py-2 px-4 shadow block w-full min-w-60 bg-white border-transparent text-center rounded-lg text-sm focus:ring-0"/>
                </div>

                <livewire:notes.chat-side-bar/>
            </div>

            <div class="mx-8 my-4">
                <livewire:apikeys.api-key-banner/>
            </div>

            <div class="flex flex-wrap gap-6 mb-8 mt-8 px-8">
                @foreach($this->notes as $note)
                    <div
                        class="flex-grow flex-shrink-0 basis-[calc(50%-1.5rem)] p-4 bg-gradient-to-b from-gray-50 to-gray-100 rounded-lg transition-shadow border relative flex flex-col"
                        wire:key="note-{{$note->id}}{{uniqid()}}">
                        <div class="relative min-h-10">

                            <div
                                x-data="{ open: false, subOpen: false, subMenuLeft: false, subMenuBottom: false, subMenuTimer: null }"
                                class="absolute top-0 right-0" x-cloak x-init="
                                    $nextTick(() => { open = false; subOpen = false; subMenuLeft = false; subMenuBottom = false; });

                                    $wire.on('updated', () => {
                                        open = false;
                                        subOpen = false;
                                        subMenuBottom = false;
                                    });

                                    Livewire.on('notesUpdated', () => {
                                        open = false;
                                        subOpen = false;
                                        subMenuBottom = false;
                                    });
                                ">

                                <button @click="open = !open" class="text-gray-500 hover:text-gray-700">
                                    <x-icons.dotsv/>
                                </button>

                                <div
                                    x-cloak
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
                                        @mouseenter="
                                            clearTimeout(subMenuTimer);
                                            subOpen = true;
                                            subMenuLeft = (window.innerWidth - $el.getBoundingClientRect().right < 150);
                                            subMenuBottom = (window.innerHeight - $el.getBoundingClientRect().bottom < 150);
                                            "
                                        @mouseleave="
                                            subMenuTimer = setTimeout(() => { subOpen = false }, 200);
                                        "
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
                                            x-cloak
                                            x-show="subOpen"
                                            @mouseenter="clearTimeout(subMenuTimer)"
                                            @mouseleave="subMenuTimer = setTimeout(() => { subOpen = false }, 200)"
                                            :class="{
                                                'right-full': subMenuLeft,
                                                'left-full': !subMenuLeft,
                                                'bottom-full': subMenuBottom,
                                                'top-0': !subMenuBottom
                                            }"
                                            class="absolute z-50 min-w-40 max-w-48 ml-0.5 bg-white shadow-lg"
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
                                <span wire:click="viewNote({{$note->id}})" class="font-semibold text-gray-700 cursor-pointer" x-data x-tooltip.raw="click to view">
                                    {{$note->title}}
                                </span>
                                <p class="mt-4 content text-sm text-gray-600 prose prose-sm sm:prose lg:prose xl:prose max-w-none w-full word-break-all break-long-words scrollbar-code">
                                    {!! Str::limit($note->content, 1000) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="items-center justify-center w-full">
                    {{ $this->notes->links() }}
                </div>

            </div>

        </main>
    </div>

    <livewire:notes.text-note :folder="$folder"/>

    <style>
        iframe {
            width: 100% !important;
            height: 400px !important;
        }

        .quill-editor iframe, .ql-editor iframe {
            pointer-events: none !important;
        }

        div.content img {
            width: 100% !important;
            max-height: 300px !important;
            height: auto !important;
            object-fit: contain !important;
        }
    </style>
</div>
