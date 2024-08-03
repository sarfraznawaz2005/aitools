<x-layouts.app :title="'Home'">

    <!-- Announcement Banner -->
    <div class="bg-gradient-to-r from-red-500 via-purple-400 to-blue-500">
        <div class="max-w-[85rem] px-4 py-4 sm:px-6 lg:px-8 mx-auto">
            <!-- Grid -->
            <div class="grid justify-center md:grid-cols-2 md:justify-between md:items-center gap-2">
                <div class="text-center md:text-start">
                    <p class="text-xs text-white/80 uppercase tracking-wider">
                        Preview of Preline
                    </p>
                    <p class="mt-1 text-white font-medium">
                        Sign up to get unlimited updates. No credit card required.
                    </p>
                </div>
                <!-- End Col -->

                <div class="mt-3 text-center md:text-start md:flex md:justify-end md:items-center">
                    <a class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                       href="#">
                        Sign up free
                    </a>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Grid -->
        </div>
    </div>
    <!-- End Announcement Banner -->

    <br>

    <!-- Announcement Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-400">
        <div class="max-w-[85rem] px-4 py-4 sm:px-6 lg:px-8 mx-auto">
            <!-- Grid -->
            <div class="grid justify-center md:grid-cols-2 md:justify-between md:items-center gap-2">
                <div class="text-center md:text-start md:order-2 md:flex md:justify-end md:items-center">
                    <p class="me-5 inline-block text-sm font-semibold text-white">
                        Ready to get started?
                    </p>
                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border-2 border-white text-white hover:border-white/70 hover:text-white/70 focus:outline-none focus:border-white/70 focus:text-white/70 disabled:opacity-50 disabled:pointer-events-none"
                       href="#">
                        Sign up
                    </a>
                </div>
                <!-- End Col -->

                <div class="flex items-center">
                    <a class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-lg font-medium text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 transition text-sm"
                       href="#">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Watch demo
                    </a>
                    <span class="inline-block border-e border-white/30 w-px h-5 mx-2"></span>
                    <a class="py-2 px-3 inline-flex justify-center items-center gap-2 rounded-lg font-medium text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 transition text-sm"
                       href="#">
                        Explore what's new
                    </a>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Grid -->
        </div>
    </div>
    <!-- End Announcement Banner -->

    <br>

    <div class="text-center">
        <button type="button"
                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-task-created-alert"
                data-hs-overlay="#hs-task-created-alert">
            Open modal
        </button>
    </div>

    <div id="hs-task-created-alert"
         class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto" role="dialog"
         tabindex="-1" aria-labelledby="hs-task-created-alert-label">
        <div
            class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
            <div class="relative flex flex-col bg-white shadow-lg rounded-xl dark:bg-neutral-900">
                <div class="absolute top-2 end-2">
                    <button type="button"
                            class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600"
                            aria-label="Close" data-hs-overlay="#hs-task-created-alert">
                        <span class="sr-only">Close</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-4 sm:p-10 text-center overflow-y-auto">
                    <!-- Icon -->
                    <span
                        class="mb-4 inline-flex justify-center items-center size-[46px] rounded-full border-4 border-green-50 bg-green-100 text-green-500 dark:bg-green-700 dark:border-green-600 dark:text-green-100">
          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
               viewBox="0 0 16 16">
            <path
                d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
          </svg>
        </span>
                    <!-- End Icon -->

                    <h3 id="hs-task-created-alert-label"
                        class="mb-2 text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Task successfully created!
                    </h3>
                    <p class="text-gray-500 dark:text-neutral-500">
                        You can see the progress of your task in your <a
                            class="inline-flex items-center gap-x-1.5 text-blue-600 decoration-2 hover:underline focus:outline-none focus:underline font-medium dark:text-blue-500"
                            href="#">personal account.</a> You will be notified of its completion.
                    </p>

                    <div class="mt-6 flex justify-center gap-x-4">
                        <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-none focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800"
                                data-hs-overlay="#hs-task-created-alert">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-layouts.app>
