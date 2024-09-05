<div>

    <div class="flex flex-col sm:flex-row bg-white">

        <livewire:notes.sidebar :folder="$folder"/>

        <main class="flex-1 pt-10 border-t sm:border-t-0 h-screen overflow-y-auto mb-4 sm:border-l border-gray-300">

            <div class="mx-4 sm:mx-8 my-4">
                <livewire:apikeys.api-key-banner/>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-20 mt-8 px-4 sm:px-6">
                @foreach($this->notes as $note)
                    <div
                        class="p-4 bg-gray-50 rounded-lg border relative flex flex-col"
                        wire:key="note-{{$note->id}}{{uniqid()}}" x-data="{
                        copied: false,
                        copy () {
                          $clipboard($refs.content.innerText)
                          this.copied = true
                          setTimeout(() => {
                            this.copied = false
                          }, 1000)
                        }
                      }">
                        <div class="relative min-h-10 flex flex-col h-full">

                            <div
                                x-data="{
                                        open: false,
                                        subOpen: false,
                                        subMenuLeft: false,
                                        subMenuBottom: false,
                                        subMenuTimer: null,
                                    }"
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
                                    class="absolute right-[4px] top-6 z-20 w-32 bg-white text-xs shadow-lg"
                                >
                                    <a href="#" @click.prevent="copy"
                                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <x-icons.copy class="inline-block mr-2 text-gray-500"/>
                                        <span
                                            x-text="typeof(copied) !== 'undefined' && copied ? 'Copied' : 'Copy'"></span>
                                    </a>

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
                                            class="absolute z-20 min-w-40 max-w-48 ml-0.5 bg-white shadow-lg"
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

                            <div class="w-full flex-grow overflow-hidden" x-ref="content">
                                <span
                                    x-data
                                    wire:click="viewNote({{$note->id}})"
                                    x-tooltip.raw="click to view"
                                    class="font-semibold text-sm text-gray-700 cursor-pointer block mb-2 mr-4">
                                    {{$note->title}}
                                </span>
                                <div
                                    wire:click="viewNote({{$note->id}})"
                                    class="content text-gray-800 cursor-pointer prose prose-sm sm:prose lg:prose xl:prose max-w-none w-full overflow-hidden">
                                    <div class="line-clamp-5">
                                        <bdi>{!! $note->content !!}</bdi>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="items-center justify-center w-full">
                    {{ $this->notes->links() }}
                </div>

            </div>

            <div
                style="box-shadow: 0 -5px 15px -6px rgba(0, 0, 0, 0.25);"
                class="flex flex-col sm:flex-row justify-between items-center w-full sm:w-[calc(100%-13rem)] border-t fixed bottom-0 z-10 py-2 px-4 {{ $folder->getBackGroundColor() }} {{ $folder->getBorderColor() }}">

                <div class="flex flex-col sm:flex-row items-center justify-between w-full sm:w-auto">

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
                            class="absolute left-[-12px] bottom-9 min-w-24 bg-white shadow-lg text-xs space-y-0.5 divide-y divide-gray-200"
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

                <div class="relative bg-transparent mt-4 sm:mt-0 w-full sm:w-auto">

                    <div>
                        <x-icons.search class="absolute top-2 left-4 text-gray-500"/>
                    </div>

                    <input type="text" wire:model.live.debounce.500ms="searchQuery"
                           placeholder="Search Content..."
                           class="py-2 pl-12 pr-6 ml-4 sm:ml-0 block w-full sm:w-auto min-w-96 bg-white shadow focus:outline-none border-none border-transparent outline-0 text-center rounded-full text-sm focus:ring-0"/>
                </div>

                <livewire:notes.chat-side-bar/>
            </div>

        </main>
    </div>

    <livewire:notes.text-note :folder="$folder"/>

    <script data-navigate-once>

        function openLinksExternally() {
            // setup mutation observer for div with content class to open any links in new tab
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === 1 && node.tagName === 'A') {
                            node.setAttribute('target', '_blank');
                        }
                    });
                });
            });

            const targetNode = document.querySelector('.content');

            if (targetNode) {
                observer.observe(targetNode, {
                    childList: true,
                    subtree: true
                });
            }

            // Also set target="_blank" for existing links on page load
            document.querySelectorAll('.content a').forEach((link) => {
                link.setAttribute('target', '_blank');
            });
        }

        document.addEventListener('DOMContentLoaded', () => openLinksExternally());
        document.addEventListener('livewire:navigated', () => openLinksExternally());
    </script>

    <style>

        /* Container for the content */
        .content {
            max-height: 15rem; /* This is equivalent to max-h-60 in Tailwind */
            overflow-y: auto;
            position: relative;
            font-size: 0.9rem;
            line-height: 1.6rem;
        }

        /* Ensure all direct children are visible */
        .content > * {
            display: block;
            visibility: visible;
            opacity: 1;
            overflow: visible;
            width: 100%;
            margin-bottom: 1rem;
        }

        /* Responsive images */
        .content img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* Style links */
        .content a {
            color: rgb(59 130 246);
            text-decoration: none;
        }

        /* Style lists */
        .content ul, .content ol {
            padding-left: 1.5rem;
        }

        .content li {
            list-style: square;
        }

        .content li::marker {
            color: #9ca3af;
        }

        /* Style headings */
        .content h1, .content h2, .content h3 {
            color: #5c616c;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        /* Style iframes (videos) */
        .content iframe {
            width: 100% !important;
            height: 14rem !important;
            margin: 1rem 0;
        }

        .content * {
            position: static !important;
        }

    </style>
</div>
