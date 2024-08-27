<div>

    <div class="flex flex-col bg-white border shadow-sm rounded-xl m-auto">
        <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:px-5" style="padding: 16px 16px 14px 16px;">
            <p class="text-sm text-gray-500 font-bold">
                🚀 Start New Conversation
            </p>
        </div>

        <div class="p-3 px-6 text-center">
            <div
                class="rounded-lg p-3 border border-gray-200 w-fit justify-center m-auto bg-gray-100 text-gray-800 text-sm">
                🎉 Hey there, click a bot to choose or start talking to the versatile General bot by default.
            </div>

            <div class="w-full flex justify-center items-center flex-wrap">
                <fieldset
                    class="items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-5 pb-7 dark:border-neutral-700 my-4">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">System Bots</legend>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 font-medium">
                        @foreach($this->bots as $bot)
                            @if($bot->system)
                                <button
                                    type="button"
                                    wire:click.prevent="selectBot({{ $bot->id }})"
                                    x-data="{
                                   isSelected: false,
                                    init() {
                                            $wire.on('botSelected', (botId) => {
                                                this.isSelected = (botId == {{ $bot->id }});
                                            });
                                        }
                                    }"
                                    :class="{ 'bg-yellow-100 hover:bg-yellow-100': isSelected }"
                                    x-tooltip.raw="{{$bot->bio}}"
                                    wire:key="bot-{{ $bot->id }}"
                                    x-ref="button"
                                    class="w-full text-sm inline-flex items-center rounded-lg border border-gray-300 text-gray-800 hover:bg-gray-200 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                >

                                    <span class="inline-flex justify-center items-center p-1 rounded-lg">
                                      <span class="inline-block text-base lg:text-2xl md:text-2xl xl:text-2xl">
                                          {{ $bot->icon }}
                                      </span>
                                    </span>

                                    <span class="truncate">{{ $bot->name }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </fieldset>

                <fieldset
                    class="items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-5 pb-7 dark:border-neutral-700 my-4">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Your Bots</legend>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4 font-medium">
                        @foreach($this->bots as $bot)
                            @if(!$bot->system)
                                <div
                                    class="flex w-full {{$bot->id === $newBotId ? 'animate-jump animate-delay-500' : ''}}">
                                    <button
                                        type="button"
                                        wire:click.prevent="selectBot({{ $bot->id }})"
                                        x-data="{
                                            isSelected: false,
                                            init() {
                                                $wire.on('botSelected', (botId) => {
                                                    this.isSelected = (botId == {{ $bot->id }});
                                                });
                                            }
                                        }"
                                        :class="{ 'bg-yellow-100 hover:bg-yellow-100': isSelected }"
                                        x-tooltip.raw="{{$bot->bio}}"
                                        wire:key="bot-{{ $bot->id }}"
                                        x-ref="button"
                                        class="w-full px-1 text-sm inline-flex items-center border hover:bg-gray-200 border-gray-300 text-gray-800 rounded-l-lg focus:outline-none disabled:opacity-50 disabled:pointer-events-none transition duration-150 ease-in-out overflow-hidden"
                                    >
                                    <span class="inline-flex justify-center items-center p-1 rounded-lg">
                                      <span class="inline-block text-base lg:text-2xl md:text-2xl xl:text-2xl">
                                          {{ $bot->icon }}
                                      </span>
                                    </span>
                                        <span class="truncate">{{ $bot->name }}</span>
                                    </button>

                                    <button
                                        type="button"
                                        x-data
                                        x-tooltip.raw="Edit"
                                        wire:click="edit({{ $bot->id }})"
                                        class="flex-shrink-0 px-2 bg-gray-100 hover:bg-gray-200 text-gray-500 border-l-0 rounded-r-lg border border-gray-300 focus:outline-none disabled:opacity-50 disabled:pointer-events-none transition duration-150 ease-in-out"
                                    >
                                        <x-icons.edit/>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="flex justify-center {{$this->bots->where('system', false)->count() > 0 ? 'mt-8' : ''}}">
                        <x-gradient-button data-hs-overlay="#botModal" wire:click="resetForm">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>

                            Create New Bot
                        </x-gradient-button>
                    </div>

                </fieldset>
            </div>

        </div>
    </div>

    <x-modal id="botModal">
        <x-slot name="title">
            <div class="flex gap-x-2">
                {{ $model->exists ? '✏️ Edit Bot' : '➕ Create Bot'}}
            </div>
        </x-slot>

        <x-slot name="body">

            <x-flash/>

            <div class="max-w-lg mx-auto p-3" x-data="{ botType: @entangle('type') }">

                <div class="relative mb-3">
                    <input type="text"
                           wire:model="name"
                           placeholder="Name"
                           autofocus
                           class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                </div>

                <div class="relative mb-3">
                    <input type="text"
                           wire:model="bio"
                           placeholder="Description"
                           autofocus
                           class="peer py-3 px-4 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                </div>

                <div class="relative mb-3" x-show="botType == '{{App\Enums\BotTypeEnum::TEXT->value}}'">
                    <!-- Textarea -->
                    <div class="relative">
                        <textarea
                            wire:model="prompt"
                            rows="3"
                            class="p-4 pb-12 block w-full bg-gray-100 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Prompt"></textarea>

                        <!-- Toolbar -->
                        <div class="absolute bottom-px inset-x-px p-2 rounded-b-md bg-gray-100">
                            <div class="flex justify-end items-center">
                                <!-- Button Group -->
                                <div class="flex items-center gap-x-1">
                                    <!-- Popover -->
                                    <div class="hs-tooltip [--trigger:hover] [--placement:top] inline-block text-xs">
                                        <span
                                            class="hs-tooltip-toggle cursor-pointer rounded-lg bg-white p-1 border border-gray-300">
                                            💡
                                            <span
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                class="hs-tooltip-content text-wrap p-4 hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[100] max-w-xs w-full bg-white border border-gray-100 text-start rounded-xl shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full"
                                                role="tooltip">
                                                <span class="font-semibold mb-2 text-xs">Prompt Example:</span><hr
                                                    class="h-1 my-2">
                                                I want you to act as an interviewer. I will be the candidate and you will ask me the interview questions for the position position. I want you to only reply as the interviewer. Do not write all the conservation at once. I want you to only do the interview with me. Ask me the questions and wait for my answers. Do not write explanations. Ask me the questions one by one like an interviewer does and wait for my answers.
                                            </span>
                                        </span>
                                    </div>
                                    <!-- End Popover -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="relative mb-3">

                    <div
                        x-data="{
                            open: false,
                            selectedIcon: @entangle('icon'),
                            iconGroups: [
                                {
                                    title: 'Professions & People',
                                    icons: ['👨‍💻', '👤', '🕵️', '🧑‍🔬', '👨‍', '👨🏻‍🏫', '👨🏻‍🏭', '🧙‍♂️', '🧕', '🧞', '🦸‍♀️', '🥷', '👷', '👼', '👨‍🏫', '🗣️', '🥸', '👨‍🍳', '👧🏼', '🧑‍🚀', '🫅']
                                },
                                {
                                    title: 'Relationships & Emotions',
                                    icons: ['🫂', '🧑🏻‍🤝‍🧑🏻', '💏', '💖', '😉', '😍', '😎', '🤠', '🤡']
                                },
                                {
                                    title: 'Fantasy & Mythical',
                                    icons: ['👽', '👹', '👺']
                                },
                                {
                                    title: 'Education & Knowledge',
                                    icons: ['📚', '🎓', '🧠']
                                },
                                {
                                    title: 'Technology',
                                    icons: ['💻', '📱']
                                },
                                {
                                    title: 'Space & Exploration',
                                    icons: ['🌎', '🚀', '🌕', '🪐', '🛸']
                                },
                                {
                                    title: 'Ideas & Creativity',
                                    icons: ['💡', '🎨', '🔮']
                                },
                                {
                                    title: 'Animals',
                                    icons: ['🐼', '🦁', '🦜', '🦄']
                                },
                                {
                                    title: 'Places & Transportation',
                                    icons: ['🏠', '🌳', '🚲', '✈️', '🕌']
                                },
                                {
                                    title: 'Objects & Tools',
                                    icons: ['🛒', '⌚', '🎥', '🎧', '📅', '📊', '📌', '👑', '👕', '📢', '🧰', '🗄️', '📝', '🖼️', '🎬', '🧮', '🪄', '🪶']
                                },
                                {
                                    title: 'Food & Nature',
                                    icons: ['🍔', '🍒', '🌸', '🌈']
                                },
                                {
                                    title: 'Finance',
                                    icons: ['💰', '💵', '💳']
                                },
                                {
                                    title: 'Celebration & Gifts',
                                    icons: ['🎠', '🪅', '🎉', '💝', '🎊', '✨']
                                },
                                {
                                    title: 'Miscellaneous',
                                    icons: ['🔥', '🤝', '✍️', '⚽', '⭐', '💪', '💼', '🍄', '🌿']
                                }
                            ]
                        }"

                        x-init="
                            $nextTick(() => {
                                $watch('$el', () => {
                                    Livewire.on('modal-opened', () => {
                                        open = false;
                                    });
                                });
                            })
                         "
                        class="relative w-full">

                        <!-- Dropdown button -->
                        <button
                            @click="open = !open"
                            class="py-3 px-4 pr-3 w-full bg-gray-100 border rounded-lg text-sm focus:border-2 focus:border-blue-500 focus:ring-blue-500 flex justify-between items-center h-12">

                            <span class="flex items-center space-x-1">
                                <span :class="selectedIcon ? 'text-lg text-gray-700' : 'text-sm text-gray-500'"
                                      x-text="selectedIcon ? selectedIcon : 'Choose Icon'">
                                </span>
                            </span>

                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown content -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform translate-y-2"
                             class="absolute bottom-full mb-1 w-full bg-white border rounded-lg shadow-lg max-h-56 overflow-y-auto z-10">
                            <div class="p-2 space-y-4">
                                <template x-for="group in iconGroups" :key="group.title">
                                    <div>
                                        <span class="text-sm font-semibold mb-2" x-text="group.title"></span>
                                        <div class="grid grid-cols-3 gap-2 w-full">
                                            <template x-for="icon in group.icons" :key="icon">
                                                <div @click="selectedIcon = icon; open = false;"
                                                     :class="{'bg-blue-100': selectedIcon === icon, 'bg-gray-100': selectedIcon !== icon}"
                                                     class="p-2 bg-gray-100 hover:bg-blue-100 cursor-pointer rounded-lg text-center">
                                                    <span x-text="icon" class="text-4xl"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="relative mb-3">
                    <select
                        wire:model="type"
                        class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                        {{--<option value="">Bot Type</option>--}}
                        <option class="text-base" value="{{App\Enums\BotTypeEnum::TEXT->value}}">
                            📝 {{App\Enums\BotTypeEnum::TEXT}}</option>
                        <option class="text-base" value="{{App\Enums\BotTypeEnum::DOCUMENT->value}}">
                            📚 {{App\Enums\BotTypeEnum::DOCUMENT}}</option>
                        {{--<option class="text-base" value="{{App\Enums\BotTypeEnum::IMAGE->value}}">🖼️ {{App\Enums\BotTypeEnum::IMAGE}}</option>--}}
                        {{--<option class="text-base" value="{{App\Enums\BotTypeEnum::VIDEO->value}}">🎬 {{App\Enums\BotTypeEnum::VIDEO}}</option>--}}
                    </select>
                </div>

                <div class="hs-accordion-group" x-show="botType == '{{App\Enums\BotTypeEnum::DOCUMENT->value}}'">
                    <span class="text-sm">Knowledge Sources</span>

                    <!-- Popover -->
                    <div class="hs-tooltip [--trigger:hover] [--placement:top] inline-block text-xs">
                                        <span
                                            class="hs-tooltip-toggle text-xs cursor-pointer rounded-lg bg-white p-1 border border-gray-300">
                                            💡
                                            <span
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                class="hs-tooltip-content text-wrap p-4 hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible hidden opacity-0 transition-opacity absolute invisible z-[100] max-w-xs w-full bg-white border border-gray-100 text-start rounded-xl shadow-md after:absolute after:top-0 after:-start-4 after:w-4 after:h-full"
                                                role="tooltip">
                                                If knowledge source files are uploaded, bot will be limited to them only
                                                allowing you to talk to uploaded files only. This means, this option allows you to create talk to document
                                                bots.
                                            </span>
                                        </span>
                    </div>
                    <!-- End Popover -->

                    <fieldset
                        class="items-center justify-center w-full border border-gray-300 rounded-lg p-5 dark:border-neutral-700 my-4">
                        <legend class="text-sm text-gray-600 dark:text-neutral-300">
                            Add Knowledge Sources
                            <span class="text-xs">(PDFs, TXTs, etc)</span>
                        </legend>

                        <p class="text-xs">For best results, it's recommmnded to have one file per bot.</p>

                        <div class="relative w-full">
                            <div
                                x-data="{ uploading: false, progress: 0 }"
                                x-on:livewire-upload-start="uploading = true"
                                x-on:livewire-upload-finish="uploading = false"
                                x-on:livewire-upload-cancel="uploading = false"
                                x-on:livewire-upload-error="uploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                            >
                                <!-- File Input -->
                                <label for="file-input" class="sr-only">Choose files</label>
                                <input type="file" wire:model="files" id="{{uniqid()}}" multiple
                                       class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400
                                                file:bg-gray-50 file:border-0
                                                file:me-4
                                                file:py-3 file:px-4
                                                dark:file:bg-neutral-700 dark:file:text-neutral-400">

                                <span class="text-xs ml-2">Max Size: 25MB</span>

                                <!-- Progress Bar -->
                                <div x-show="uploading">
                                    <progress max="100" x-bind:value="progress"></progress>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    @if($botFiles)
                        <fieldset
                            class="items-center justify-center w-full border border-gray-300 rounded-lg p-5 dark:border-neutral-700 my-4">
                            <legend class="text-sm text-gray-600 dark:text-neutral-300">
                                Uploaded Files
                            </legend>
                            @foreach($botFiles as $file)
                                <div class="flex items center justify-between mb-2"
                                     style="font-size: .8rem;">
                                    <div>{{basename($file)}}</div>
                                    <div class="cursor-pointer"
                                         wire:click="deleteFile('{{basename($file)}}')">
                                        <x-icons.delete class="text-red-500"/>
                                    </div>
                                </div>
                            @endforeach
                        </fieldset>
                    @endif

                </div>

                <div class="relative mb-3">
                    <div class="flex">
                        <input type="checkbox"
                               wire:model="related_questions"
                               class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                               id="hs-default-checkbox">
                        <label for="hs-default-checkbox" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Allow
                            Related Questions</label>
                    </div>
                </div>

            </div>

            <div
                class="flex items-center border-t border-gray-300 pt-4 {{$model->exists ? 'justify-between' : 'justify-end'}}">
                @if ($model->exists)
                    <x-confirm-dialog call="delete({{ $model->id }})"
                                      text="Are you sure you want to delete?"
                                      class="font-bold flex items-center justify-center bg-red-500 hover:bg-red-600 gap-x-3 py-2 px-3 text-sm text-white rounded-lg focus:outline-none focus:ring-0 focus:ring-offset-0">
                        <x-icons.delete/>
                        Delete
                    </x-confirm-dialog>
                @endif

                <x-gradient-button wire:click="save">
                    <x-icons.ok class="size-5"/>
                    Save
                </x-gradient-button>
            </div>

        </x-slot>
    </x-modal>

</div>
