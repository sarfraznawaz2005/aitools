<x-layouts.app :title="'Home'">
    <!-- Icon Blocks -->
    <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 items-center gap-6 md:gap-10">
            <!-- Card -->
            <div class="size-full bg-white shadow-lg rounded-lg p-5 dark:bg-neutral-900 text-center">
                <x-icons.chat color="#8C33FF" class="shrink-0 size-12" />
                <h3 class="block text-lg font-semibold text-gray-800 dark:text-white">Chat Buddy</h3>
                <p class="text-gray-600 dark:text-neutral-400">Start Conversation with Chat Buddy.</p>
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="size-full bg-white shadow-lg rounded-lg p-5 dark:bg-neutral-900">
                <div class="flex items-center gap-x-4 mb-3">
                    <div class="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-blue-50 bg-blue-100 dark:border-blue-900 dark:bg-blue-800">
                        <svg class="shrink-0 size-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h20"/><path d="M21 3v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3"/><path d="m7 21 5-5 5 5"/></svg>
                    </div>
                    <div class="shrink-0">
                        <h3 class="block text-lg font-semibold text-gray-800 dark:text-white">Get freelance work</h3>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-neutral-400">New design projects delivered to your inbox each morning.</p>
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="size-full bg-white shadow-lg rounded-lg p-5 dark:bg-neutral-900">
                <div class="flex items-center gap-x-4 mb-3">
                    <div class="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-blue-50 bg-blue-100 dark:border-blue-900 dark:bg-blue-800">
                        <svg class="shrink-0 size-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/></svg>
                    </div>
                    <div class="shrink-0">
                        <h3 class="block text-lg font-semibold text-gray-800 dark:text-white">Sell your goods</h3>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-neutral-400">Get your goods in front of millions of potential customers with ease.</p>
            </div>
            <!-- End Card -->

        </div>
    </div>
    <!-- End Icon Blocks -->
</x-layouts.app>
