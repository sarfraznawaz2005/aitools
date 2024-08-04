<div>

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Sidebar -->
    <div id="hs-application-sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full duration-300 transform hidden fixed top-14 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700" role="dialog" tabindex="-1" aria-label="Sidebar">
        <nav class="size-full flex flex-col">

            <div class="h-full">
                <!-- List -->
                <ul class="space-y-1.5 p-4">
                    <li>
                        <a class="flex items-center gap-x-3 py-2 px-3 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-300 dark:focus:bg-neutral-900 dark:focus:text-neutral-300" href="#">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            New chat
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center gap-x-3 py-2 px-3 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-300 dark:focus:bg-neutral-900 dark:focus:text-neutral-300" href="#">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" x2="12" y1="15" y2="3" />
                            </svg>
                            Save conversation
                        </a>
                    </li>
                </ul>
                <!-- End List -->
            </div>

            <!-- Footer -->
            <div class="mt-auto">
                <div class="py-2.5 px-7">
                    <p class="inline-flex items-center gap-x-2 text-xs text-green-600">
                        <span class="block size-1.5 rounded-full bg-green-600"></span>
                        Active 12,320 people
                    </p>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-neutral-700">
                    <a class="flex justify-between items-center gap-x-3 py-2 px-3 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:hover:bg-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-300 dark:focus:text-neutral-300" href="#">
                        Sign in
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" x2="3" y1="12" y2="12" />
                        </svg>
                    </a>
                </div>
            </div>
            <!-- End Footer -->
        </nav>
    </div>
    <!-- End Sidebar -->

    <!-- Content -->
    <div class="relative h-screen w-full lg:ps-64">
        <div class="py-4">

            <ul class="space-y-5">

                <!-- Chat Bubble -->
                <li class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
                    <div class="grow text-end space-y-3">
                        <!-- Card -->
                        <div class="inline-block bg-blue-600 rounded-lg p-4 shadow-sm">
                            <p class="text-sm text-white">
                                what's preline ui?
                            </p>
                        </div>
                        <!-- End Card -->
                    </div>

                    <x-icons.user class="shrink-0 size-[50px] rounded-full" />
                </li>
                <!-- End Chat Bubble -->

                <!-- Chat Bubble -->
                <li class="flex gap-x-2 sm:gap-x-4">
                    <x-icons.ai class="shrink-0 size-[40px] rounded-full" viewBox="-100 -159 1000 1000" />

                    <div class="grow max-w-[90%] md:max-w-2xl w-full space-y-3">
                        <!-- Card -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-3 dark:bg-neutral-900 dark:border-neutral-700">
                            <p class="text-sm text-gray-800 dark:text-white">
                                Preline UI is an open-source set of prebuilt UI components based on the utility-first Tailwind CSS framework.
                            </p>
                        </div>
                        <!-- End Card -->

                        <!-- Button Group -->
                        <div>
                            <div class="sm:flex sm:justify-between">
                                <div>
                                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        <x-icons.copy/>
                                        Copy
                                    </button>
                                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        <x-icons.refresh/>
                                        Regenerate
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Button Group -->
                    </div>
                </li>
                <!-- End Chat Bubble -->
            </ul>
        </div>

        <!-- Textarea -->
        <div class="lg:w-[170vh] md:w-[110vh] sm:w-[110vh] mx-auto fixed bottom-0 z-10 p-3 sm:py-6">
            <div class="lg:hidden flex justify-end mb-2 sm:mb-3">
                <!-- Sidebar Toggle -->
                <button type="button" class="p-2 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-application-sidebar" aria-label="Toggle navigation" data-hs-overlay="#hs-application-sidebar">
                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6" />
                        <line x1="3" x2="21" y1="12" y2="12" />
                        <line x1="3" x2="21" y1="18" y2="18" />
                    </svg>
                    <span>Sidebar</span>
                </button>
                <!-- End Sidebar Toggle -->
            </div>

            <!-- Input -->
            <div class="flex w-full items-center">
                <div class="flex w-full flex-col gap-1.5 rounded-full p-1 transition-colors bg-gray-200 dark:bg-token-main-surface-secondary">
                    <div class="flex items-end gap-1.5 md:gap-2">

                        <div class="flex min-w-0 flex-1 flex-col">
                            <textarea
                                id="prompt-textarea"
                                tabindex="0"
                                autofocus
                                dir="auto"
                                rows="1"
                                placeholder="Ask me anything..."
                                class="m-0 resize-none border-0 rounded-full px-4 focus:ring-0 focus-visible:ring-0"
                                style="height: 40px; overflow-y: hidden;"></textarea>
                        </div>

                        <button data-testid="send-button" class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-black text-white transition-colors hover:opacity-70 focus-visible:outline-none focus-visible:outline-black disabled:bg-[#D7D7D7] disabled:text-[#f4f4f4] disabled:hover:opacity-100 dark:bg-white dark:text-black dark:focus-visible:outline-white disabled:dark:bg-token-text-quaternary dark:disabled:text-token-main-surface-secondary" disabled="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32" class="icon-2xl">
                                <path fill="currentColor" fill-rule="evenodd" d="M15.192 8.906a1.143 1.143 0 0 1 1.616 0l5.143 5.143a1.143 1.143 0 0 1-1.616 1.616l-3.192-3.192v9.813a1.143 1.143 0 0 1-2.286 0v-9.813l-3.192 3.192a1.143 1.143 0 1 1-1.616-1.616z" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                    </div>
                </div>
            </div>
            <!-- End Input -->
        </div>
        <!-- End Textarea -->
    </div>
    <!-- End Content -->
    <!-- ========== END MAIN CONTENT ========== -->

</div>


