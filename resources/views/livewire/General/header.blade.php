@php
    $currentRoute = Route::currentRouteName();
@endphp

<div>
    <!-- header start -->
    <header
        class="top-0 inset-x-0 flex flex-wrap w-full bg-white text-sm dark:bg-neutral-900 z-[80]"
        style="position: fixed;">
        <nav class="px-4 flex basis-full items-center w-full mx-auto">
            <div class="w-full flex items-center ms-auto justify-between">

                <div class="inline-flex gap-4 items-center">

                    <a href="{{route('home')}}" wire:navigate>
                        <x-icons.home
                            class="shrink-0 size-7 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500"/>
                    </a>

                    <div class="border-r py-4 border-r-gray-300 dark:border-r-neutral-600">&nbsp;</div>

                    <!-- Dropdown -->
                    <div class="hs-dropdown [--placement:center] relative inline-flex items-center">
                        <button id="hs-dropdown-account" type="button"
                                class="inline-flex justify-center items-center text-gray-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:text-white"
                                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            <x-icons.dots
                                class="h-8 w-8 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500"/>
                        </button>

                        <div
                            class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-40 bg-gray-200 dark:bg-neutral-900 shadow-md rounded border dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full"
                            role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-account">
                            <div class="p-1.5 space-y-0.5">
                                <ul class="flex flex-col space-y-1">
                                    @foreach(config('tools') as $tool)
                                        <li wire:key="{{ $tool['name'] }}">
                                            <a href="{{route($tool['route'])}}" wire:navigate
                                               class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ str_contains(strtolower($currentRoute), strtolower($tool['route'])) ? 'bg-gray-100' : '' }}">
                                                <x-dynamic-component :component="'icons.' . $tool['icon']['name']"
                                                                     :color="$tool['icon']['color']"/>
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
                        <h2 class="text-2xl text-gray-500 font-semibold dark:text-gray-300 md:mr-24 lg:mr-24">{{ $title }}</h2>
                    @else
                        <a href="{{route('home')}}" wire:navigate>
                            <h2 class="text-2xl text-gray-500 font-semibold dark:text-gray-300 md:mr-24 lg:mr-24 hover:text-gray-700 dark:hover:text-gray-500">{{ $title }}</h2>
                        </a>
                    @endif
                </div>

                <div class="flex flex-row items-center justify-end gap-1">
                    {{--
                    <button type="button"
                            x-data x-tooltip.raw="Change Theme"
                            class="hs-dark-mode-active:hidden block hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            data-hs-theme-click-value="dark">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <x-icons.moon/>
                        </span>
                    </button>

                    <button type="button"
                            x-data x-tooltip.raw="Change Theme"
                            class="hs-dark-mode-active:block hidden hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            data-hs-theme-click-value="light">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <x-icons.sun color="#FF8C33"/>
                        </span>
                    </button>
                    --}}

                    <button type="button"
                            x-data x-tooltip.raw="Settings"
                            class="size-[38px] relative inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                            aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-basic-modal"
                            data-hs-overlay="#general-modal">
                        <x-icons.settings
                            class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500"/>
                        <span class="sr-only">Settings</span>
                    </button>

                </div>
            </div>
        </nav>

        <div class="w-full h-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-600"></div>
    </header>
    <!-- header end -->

    <x-modal>
        <x-slot name="title">
            <div class="flex gap-x-2">
                <x-icons.settings class="shrink-0 size-6 "/>
                Settings
            </div>
        </x-slot>

        <x-slot name="body">

            <div class="border-b border-gray-200 dark:border-neutral-700">
                <nav class="flex gap-x-1" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 dark:hs-tab-active:bg-neutral-800 dark:hs-tab-active:border-b-gray-800 dark:hs-tab-active:text-white -mb-px py-3 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200 active"
                            id="card-type-tab-item-1" aria-selected="true" data-hs-tab="#apk-keys-tab"
                            aria-controls="apk-keys-tab" role="tab">
                        API Keys
                    </button>
                    <button type="button"
                            class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 dark:hs-tab-active:bg-neutral-800 dark:hs-tab-active:border-b-gray-800 dark:hs-tab-active:text-white -mb-px py-3 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-none focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200"
                            id="card-type-tab-item-2" aria-selected="false" data-hs-tab="#chatbuddy-tab"
                            aria-controls="chatbuddy-tab" role="tab">
                        Chat Buddy
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
            </div>

        </x-slot>
    </x-modal>

</div>
