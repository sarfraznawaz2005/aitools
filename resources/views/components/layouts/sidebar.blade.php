@php
    $currentRoute = Route::currentRouteName();
@endphp

<!-- sidebar start -->
<div id="hs-application-sidebar" class="transition-all pr-2 duration-300 transform h-full min-h-screen inset-y-0 start-0 bg-white border-e border-gray-200 dark:bg-neutral-900 dark:border-neutral-700 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0" role="dialog" tabindex="-1" aria-label="Sidebar">
    <div class="relative flex flex-col h-full max-h-full">
        <div class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-800 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-600">
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col space-y-1">
                    <li>
                        <a href="{{ route('home') }}" class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'home' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                            <x-icons.home color="#3357FF" />
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chat-buddy') }}" class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'chat-buddy' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                            <x-icons.chat color="#8C33FF" />
                            Chat Buddy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('text-styler') }}" class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'text-styler' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                            <x-icons.magic color="#14B8A6" />
                            Text Styler
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tips-notifier') }}" class="font-semibold w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-500 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700 {{ $currentRoute === 'tips-notifier' ? 'bg-gray-100 dark:bg-neutral-800' : '' }}">
                            <x-icons.bulb color="#FF33A1" />
                            Tips Notifier
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar end -->
