@php
    $currentRoute = Route::currentRouteName();
@endphp

<div>
    <!-- header start -->
    <header
        class="sticky top-0 inset-x-0 flex flex-wrap md:justify-start md:flex-nowrap w-full bg-white text-sm py-2.5 dark:bg-neutral-900">
        <nav class="px-4 sm:px-6 flex basis-full items-center w-full mx-auto">
            <div class="w-full flex items-center ms-auto justify-between gap-x-1 md:gap-x-3">

                <!-- Dropdown -->
                <div class="hs-dropdown [--placement:center] relative inline-flex">
                    <button id="hs-dropdown-account" title="Menu" type="button"
                            class="inline-flex justify-center items-center text-gray-800 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:text-white"
                            aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        </svg>
                    </button>

                    <div
                        class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-40 bg-gray-200 dark:bg-neutral-900 shadow-md rounded-lg border dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full"
                        role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-account">
                        <div class="p-1.5 space-y-0.5">
                            <ul class="flex flex-col space-y-1">
                                <li>
                                    <a href="{{ route('home') }}"  wire:navigate class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'home' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                                        <x-icons.home color="#3357FF" />
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('chat-buddy') }}"  wire:navigate class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'chat-buddy' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                                        <x-icons.chat color="#8C33FF" />
                                        Chat Buddy
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('text-styler') }}"  wire:navigate class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'text-styler' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                                        <x-icons.magic color="#14B8A6" />
                                        Text Styler
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('tips-notifier') }}"  wire:navigate class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'tips-notifier' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                                        <x-icons.bulb color="#FF33A1" />
                                        Tips Notifier
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- End Dropdown -->

                <div class="md:block">
                    <h2 class="text-2xl text-gray-500 font-semibold dark:text-white">{{ $title }}</h2>
                </div>

                <div class="flex flex-row items-center justify-end gap-1">
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

                    <button type="button"
                            title="Settings"
                            class="size-[38px] relative inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                            aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-basic-modal"
                            data-hs-overlay="#general-modal">
                        <x-icons.settings/>
                        <span class="sr-only">Settings</span>
                    </button>

                </div>
            </div>
        </nav>
    </header>
    <div class="w-full h-0.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-600"></div>
    <!-- header end -->

    <x-modal>
        <x-slot name="title">
            <div class="flex gap-x-2">
                <x-icons.settings class="shrink-0 size-6"/>
                Settings
            </div>
        </x-slot>

        <x-slot name="body">
            @livewire('api-keys-form')
        </x-slot>
    </x-modal>

</div>
