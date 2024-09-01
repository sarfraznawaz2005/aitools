@php($tools = config('tools'))

<div x-data="{ openDropdown: null }">
    <li>
        <input wire:model.live.debounce.500ms="search"
               type="text"
               autocomplete="off"
               placeholder="Search Conversations"
               class="w-full px-3 py-2 text-sm bg-gray-50 text-gray-700 placeholder-gray-400 border-none border-0 focus:outline-none text-center focus:ring-0"/>
    </li>

    @foreach($this->conversations as $conversationItem)
        <li x-data="{
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
            x-cloak
            wire:key="conv-{{$conversationItem->id}}"
            class="my-0.5 mt-0 conversation relative group hover:bg-gray-200 focus:outline-none {{$conversation && $conversation->id === $conversationItem->id ? 'bg-gray-200' : ''}}">

            <div class="flex justify-between">
                <div class="w-full flex items-center">
                    <a wire:navigate
                       x-show="!editable"
                       class="flex-nowrap text-sm text-gray-700 block w-full p-2"
                       href="{{route($tools['chat-buddy']['route'] . '.loadconversation', $conversationItem->id)}}">

                        <div class="flex max-w-48 w-48 overflow-hidden truncate whitespace-nowrap text-ellipsis items-center">
                            <div class="inline-block lg:text-2xl md:text-2xl xl:text-2xl mr-1">
                                <span class="inline-block">{{getBotIcon($conversationItem)}}</span>
                            </div>

                            <div>
                                @if($conversationItem->title)
                                    {{ucwords($conversationItem->title)}}
                                @else
                                    Conversation #{{$conversationItem->id}}
                                @endif
                            </div>
                        </div>
                    </a>

                    <div x-show="editable"
                         x-ref="titleEditable"
                         @blur="stopEdit"
                         @keydown="handleKeyDown"
                         contenteditable="true"
                         class="items-center w-full p-3 flex-nowrap text-sm text-gray-700 block border focus:outline-gray-400 bg-yellow-50">
                        {{$conversationItem->title ?? "Conversation #" . $conversationItem->id}}
                    </div>
                </div>

                <div class="flex justify-end items-center mr-2" x-show="!editable">
                    @if($conversationItem->favorite)
                        <x-icons.star class="inline-block text-gray-500 size-4 mt-1" viewBox="0 0 24 24"/>
                    @endif

                    <div>
                        <button
                            @click.prevent.stop="openDropdown = (openDropdown === {{$conversationItem->id}}) ? null : {{$conversationItem->id}}"
                            class="ml-auto cursor-pointer hidden group-hover:inline-block pr-2">
                            <x-icons.dots class="inline-block"/>
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="openDropdown === {{$conversationItem->id}}"
                 @click.away="openDropdown = null"
                 class="absolute right-[4px] bg-white border text-xs border-gray-200 shadow-lg z-10">
                <ul>
                    <li>
                        <a href="#"
                           wire:click.prevent="toggleFavorite({{$conversationItem->id}}); openDropdown = null;"
                           class="block w-full px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                            <x-icons.star class="inline-block mr-2 text-gray-500"/>
                            {{$conversationItem->favorite ? 'Un-favorite' : 'Favorite'}}
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           @click.prevent="startEdit(); openDropdown = null;"
                           class="block w-full px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                            <x-icons.edit class="inline-block mr-2 text-gray-500"/>
                            Rename
                        </a>
                    </li>
                    <li>
                        <a href="#"
                           wire:click.prevent="toggleArchived({{$conversationItem->id}}); openDropdown = null;"
                           class="block w-full px-3 py-2 text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                            <x-icons.archive class="inline-block mr-2 text-gray-500"/>
                            {{$conversationItem->archived ? 'Un-archive' : 'Archive'}}
                        </a>
                    </li>
                    <li>
                        <x-confirm-dialog call="delete({{$conversationItem->id}})" title="Delete"
                                          class="px-3 py-2 text-left block bg-white hover:bg-gray-100 w-full">
                            <x-icons.delete class="inline-block mr-2 text-red-500"/>
                            Delete
                        </x-confirm-dialog>
                    </li>
                </ul>
            </div>
        </li>
    @endforeach
</div>
