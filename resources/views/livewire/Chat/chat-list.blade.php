<div class="py-1">

    <ul class="space-y-5">

        @if(!App\Models\ApiKey::hasApiKeys())
            <li class="mb-5">
                <livewire:apikeys.api-key-banner/>
            </li>
        @endif

        <!-- Chat Bubble -->
        <li class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
            <div class="grow space-y-3">
                <!-- Card -->
                <div class="inline-block bg-gray-300 rounded-lg p-4 shadow-sm">
                    <p class="text-sm leading-loose text-gray-950" style="font-size: 98%;">
                        Preline UI is an open-source set of prebuilt UI components based on the utility-first
                        Tailwind CSS framework.
                    </p>
                </div>
                <!-- End Card -->
            </div>
        </li>
        <!-- End Chat Bubble -->

        <!-- Chat Bubble -->
        <li class="flex gap-x-2 sm:gap-x-4">
            <div class="grow w-full space-y-3">
                <!-- Card -->
                <div
                    class="bg-white border border-gray-200 rounded-lg p-4 space-y-2 dark:bg-neutral-900 dark:border-neutral-700">
                    <p class="text-sm leading-loose text-gray-800 dark:text-white" style="font-size: 98%;">
                        Preline UI is an open-source set of prebuilt UI components based on the utility-first
                        Tailwind CSS framework.
                        Preline UI is an open-source set of prebuilt UI components based on the utility-first
                        Tailwind CSS framework.
                        Preline UI is an open-source set of prebuilt UI components based on the utility-first
                        Tailwind CSS framework.
                    </p>
                </div>
                <!-- End Card -->

                <!-- Button Group -->
                <div class="sm:flex sm:justify-between">
                    <div class="mt-[-5px]">
                        <button type="button"
                                class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <x-icons.copy/>
                            Copy
                        </button>
                        <button type="button"
                                class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                            <x-icons.refresh/>
                            Regenerate
                        </button>
                    </div>
                </div>
                <!-- End Button Group -->
            </div>
        </li>
        <!-- End Chat Bubble -->
    </ul>
</div>
