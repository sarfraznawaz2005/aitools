<!-- sidebar start -->
<div id="hs-application-sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform w-[200px] h-full hidden fixed inset-y-0 start-0 z-[60] bg-white border-e border-gray-200 dark:bg-neutral-900 dark:border-neutral-700 lg:block lg:translate-x-0 lg:end-auto lg:bottom-0" role="dialog" tabindex="-1" aria-label="Sidebar">
    <div class="relative flex flex-col h-full max-h-full">
        <div class="px-6 pt-4">
            <a class="rounded-xl text-xl block font-semibold focus:outline-none text-center focus:opacity-80 dark:text-neutral-200" href="#">
                AiTools
            </a>
        </div>
        <div class="h-full overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-800 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-600">
            <nav class="hs-accordion-group p-3 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                <ul class="flex flex-col space-y-1">
                    <li>
                        <a class="flex items-center gap-x-3 py-2 px-2.5 bg-gray-100 text-sm text-gray-800 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" href="#">
                            <x-icons.home />
                            Home
                        </a>
                    </li>
                    <li>
                        <a class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700" href="#">
                            <x-icons.chat/>
                            Chat Buddy
                        </a>
                    </li>
                    <li>
                        <a class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700" href="#">
                            <x-icons.magic/>
                            Text Styler
                        </a>
                    </li>
                    <li>
                        <a class="w-full flex items-center gap-x-3 py-2 px-2.5 text-sm text-gray-800 rounded-lg hover:bg-gray-100 dark:text-neutral-200 dark:hover:bg-neutral-700" href="#">
                            <x-icons.bulb/>
                            Tips Notifier
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar end -->
