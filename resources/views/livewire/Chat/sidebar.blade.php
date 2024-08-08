@php($tools = config('tools'))

<!-- Sidebar -->
<div id="hs-application-sidebar"
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full duration-300 transform hidden fixed top-14 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700"
     role="dialog" tabindex="-1" aria-label="Sidebar" x-data="{ openDropdown: null }">

    <nav class="size-full flex flex-col h-full">

        <div class="h-full overflow-y-auto flex-1">

            <!-- List -->
            <ul class="space-y-1.5">

                @if(hasApiKeysCreated())
                    <li class="p-4">
                        <x-gradient-link class="w-full" href="/{{$tools['chat-buddy']['route']}}" wire:navigate>
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>
                            New Conversation
                        </x-gradient-link>
                    </li>
                @endif

                @foreach($conversations as $conversationItem)
                        <li wire:key="conv-{{$conversationItem->id}}"
                            x-data="{
                                editable: false,
                                startEdit() {
                                    this.editable = true;
                                    this.$nextTick(() => this.$refs.titleEditable.focus());
                                },
                                stopEdit() {
                                    if (this.editable) {
                                        this.$wire.rename({{$conversationItem->id}}, this.$refs.titleEditable.innerText);
                                        this.editable = false;
                                    }
                                },
                                handleKeyDown(event) {
                                    if (event.key === 'Enter') {
                                        event.preventDefault();
                                        this.stopEdit();
                                    }
                                }
                            }"
                            class="conversation relative group hover:bg-gray-200 focus:outline-none {{$conversation && $conversation->id === $conversationItem->id ? 'bg-gray-200' : ''}}">

                            <div class="flex justify-between">
                                <a wire:navigate
                                   x-show="!editable"
                                   class="items-center py-2 px-3 flex-nowrap text-sm text-gray-700 block w-full"
                                   href="{{route($tools['chat-buddy']['route'] . 'load-conversation', $conversationItem)}}">

                                    <span x-ref="titleDisplay">
                                        @if($conversationItem->title)
                                            {{Str::limit($conversationItem->title, 25)}}
                                        @else
                                            Conversation #{{$conversationItem->id}}
                                        @endif
                                    </span>
                                </a>

                                <div x-show="editable"
                                     x-ref="titleEditable"
                                     @blur="stopEdit"
                                     @keydown="handleKeyDown"
                                     contenteditable="true"
                                     class="items-center py-2 px-3 flex-nowrap text-sm text-gray-700 block w-full outline-none bg-yellow-50 rounded">
                                    {{$conversationItem->title ?? "Conversation #" . $conversationItem->id}}
                                </div>

                                <button
                                    @click.prevent.stop="openDropdown = openDropdown === {{$conversationItem->id}} ? null : {{$conversationItem->id}}"
                                    class="ml-auto cursor-pointer hidden group-hover:inline-block pr-2">
                                    <x-icons.dots class="inline-block"/>
                                </button>
                            </div>

                            <div x-cloak x-show="openDropdown === {{$conversationItem->id}}"
                                 @click.away="openDropdown = null"
                                 class="absolute right-[4px] bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700 z-10">
                                <ul class="py-1">
                                    <li>
                                        <a href="#"
                                           @click.prevent="startEdit(); openDropdown = null;"
                                           class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                                            <x-icons.edit class="inline-block mr-2 text-gray-500"/>
                                            Rename
                                        </a>
                                    </li>
                                    <li>
                                        <x-confirm-dialog :id="$conversationItem->id" using="delete" title="Delete"
                                                          class="pr-4 block py-2 text-sm bg-white hover:bg-gray-100 w-full">
                                            <x-icons.delete class="inline-block mr-2 text-red-500"/>
                                            Delete
                                        </x-confirm-dialog>
                                    </li>
                                </ul>
                            </div>
                        </li>
                @endforeach

            </ul>
            <!-- End List -->
        </div>

        @if (hasApiKeysCreated())
            <div
                class="sticky bottom-0 border-gray-200 dark:border-neutral-700 bg-gray-200 dark:bg-neutral-900 p-1">
                <livewire:general.model-selector for="ChatBuddy"/>
            </div>
        @endif

    </nav>
</div>
<!-- End Sidebar -->
