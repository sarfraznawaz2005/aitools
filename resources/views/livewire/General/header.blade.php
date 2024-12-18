@php
    $currentRoute = Route::currentRouteName();
@endphp

<div>

    @if($showHeader)
        <!-- header start -->
        <header
            class="top-0 fixed inset-x-0 flex flex-wrap w-full bg-white text-sm z-[50]">
            <nav class="flex basis-full items-center w-full mx-auto">
                <div class="w-full flex items-center ms-auto justify-between">

                    <div class="inline-flex items-center py-0.5">

                        <a href="{{route('home')}}" wire:navigate.hover>
                            <div class="inline-flex items-center px-4 ml-1 mt-1">
                                <x-icons.home
                                    class="shrink-0 size-7 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500"/>

                            </div>
                        </a>

                        <div class="inline-flex border-r py-3 border-r-gray-300 dark:border-r-neutral-600">&nbsp;</div>

                        <!-- Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button
                                @click="open = !open"
                                type="button"
                                class="inline-flex items-center ml-4 mt-2"
                            >
                                <x-icons.dots class="h-8 w-8 text-gray-500 hover:text-gray-700"/>
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
                                class="absolute left-0 mt-1 z-20 bg-white min-w-48 shadow-lg space-y-0.5 divide-y divide-gray-200"
                                role="menu"
                                style="min-width: max-content;"
                            >
                                <div class="py-2 first:pt-0 last:pb-0">
                                    <ul class="flex flex-col space-y-0.5">
                                        @foreach(config('tools') as $tool)
                                            <li wire:key="{{ $tool['name'] }}">
                                                <a href="{{route($tool['route'])}}" wire:navigate.hover
                                                   class="font-semibold w-full flex items-center gap-x-2 px-5 py-2 text-sm text-gray-500 hover:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ str_contains(strtolower($currentRoute), strtolower($tool['route'])) ? 'bg-gray-200' : '' }}"
                                                >
                                                    <img width="24" loading="lazy" height="24" alt="{{$tool['name']}}"
                                                         src="{{$tool['icon_data']}}">

                                                    {{$tool['name']}}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- End Dropdown -->
                    </div>

                    <div class="md:block">
                        @if($currentRoute === 'home')
                            <h2>&nbsp;</h2>
                        @else
                            <a href="{{route('home')}}" wire:navigate.hover>
                                <h2 class="text-xl text-gray-500 font-[600] md:mr-24 lg:mr-24 hover:text-gray-700">
                                    <img width="32" height="32" loading="lazy" class="inline" alt="{{$title}}"
                                         src="{{config('tools.' . Request::segment(1) . '.icon_data')}}">
                                    {{ $title }}
                                </h2>
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-row items-center justify-end gap-1">
                        <button type="button"
                                x-data x-tooltip.raw="Settings"
                                class="size-[38px] relative inline-flex justify-center items-center gap-x-2 mr-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-basic-modal"
                                data-hs-overlay="#general-modal">
                            <x-icons.settings
                                class="text-gray-500 hover:text-gray-700"/>
                            <span class="sr-only">Settings</span>
                        </button>

                    </div>
                </div>
            </nav>

            <div class="w-full h-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-600"></div>
        </header>
    @endif
    <!-- header end -->

    <x-modal id="general-modal" maxWidth="sm:max-w-xl">
        <x-slot name="title">
            <div class="flex gap-x-2">
                <x-icons.settings class="shrink-0 size-6 "/>
                Settings
            </div>
        </x-slot>

        <x-slot name="body">

            <div class="border-b border-gray-200 dark:border-neutral-700">
                <nav class="flex gap-x-1" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
                    <button type="button" x-intersect="$el.click()"
                            class="active hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 -mb-px py-2 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-1" aria-selected="true" data-hs-tab="#apk-keys-tab"
                            aria-controls="apk-keys-tab" role="tab">
                        API Keys
                    </button>
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 -mb-px py-2 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-2" aria-selected="false" data-hs-tab="#chatbuddy-tab"
                            aria-controls="chatbuddy-tab" role="tab">
                        Chat Buddy
                    </button>
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 -mb-px py-2 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-2" aria-selected="false" data-hs-tab="#tips-tab"
                            aria-controls="chatbuddy-tab" role="tab">
                        Tips Notifier
                    </button>
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 -mb-px py-2 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-2" aria-selected="false" data-hs-tab="#others-tab"
                            aria-controls="chatbuddy-tab" role="tab">
                        Others
                    </button>
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 -mb-px py-2 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-2" aria-selected="false" data-hs-tab="#backup-tab"
                            aria-controls="chatbuddy-tab" role="tab">
                        Backup
                    </button>
                </nav>
            </div>

            <div class="mt-3">
                <div id="apk-keys-tab" role="tabpanel" aria-labelledby="card-type-tab-item-1">
                    <livewire:apikeys.api-keys-form/>
                </div>
                <div id="chatbuddy-tab" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                    <livewire:settings.chat-buddy-settings/>
                </div>
                <div id="tips-tab" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                    <livewire:settings.tips-settings/>
                </div>
                <div id="others-tab" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                    <livewire:settings.others/>
                </div>
                <div id="backup-tab" class="hidden" role="tabpanel" aria-labelledby="card-type-tab-item-2">
                    <livewire:settings.backup/>
                </div>
            </div>

        </x-slot>
    </x-modal>

</div>
