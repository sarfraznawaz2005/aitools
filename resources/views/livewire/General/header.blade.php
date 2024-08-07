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
                        <button id="hs-dropdown-account" title="Menu" type="button"
                                class="inline-flex justify-center items-center text-gray-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:text-white"
                                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            <x-icons.dots class="h-8 w-8 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500" />
                        </button>

                        <div
                            class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-40 bg-gray-200 dark:bg-neutral-900 shadow-md rounded-lg border dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full"
                            role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-account">
                            <div class="p-1.5 space-y-0.5">
                                <ul class="flex flex-col space-y-1">
                                    @foreach(config('tools') as $tool)
                                        <li wire:key="{{ $tool['name'] }}">
                                            <a href="{{route($tool['route'])}}" wire:navigate
                                               class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === $tool['route'] ? 'bg-gray-100' : '' }}">
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
                            title="Change Theme"
                            class="hs-dark-mode-active:hidden block hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            data-hs-theme-click-value="dark">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <x-icons.moon/>
                        </span>
                    </button>

                    <button type="button"
                            title="Change Theme"
                            class="hs-dark-mode-active:block hidden hs-dark-mode font-medium text-gray-800 rounded-full hover:bg-gray-200 focus:outline-none focus:bg-gray-200 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                            data-hs-theme-click-value="light">
                        <span class="group inline-flex shrink-0 justify-center items-center size-9">
                            <x-icons.sun color="#FF8C33"/>
                        </span>
                    </button>
                    --}}

                    <button type="button"
                            title="Settings"
                            class="size-[38px] relative inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                            aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-basic-modal"
                            data-hs-overlay="#general-modal">
                        <x-icons.settings class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-500" />
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
            <livewire:apikeys.api-keys-form/>
        </x-slot>
    </x-modal>

</div>
