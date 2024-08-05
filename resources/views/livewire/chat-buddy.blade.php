@php
    use App\Models\ApiKey;
@endphp

<style>
    .group:hover .group-hover\:inline-block {
        display: inline-block;
    }
</style>

<div>

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Sidebar -->
    <div id="hs-application-sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full duration-300 transform hidden fixed top-14 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700" role="dialog" tabindex="-1" aria-label="Sidebar">
        <nav class="size-full flex flex-col">

            <div class="h-full">

                <!-- List -->
                <ul class="space-y-1.5 p-4">

                    <li class="mb-5">
                        <x-gradient-button class="w-full">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14" />
                                <path d="M12 5v14" />
                            </svg>
                            New Conversation
                        </x-gradient-button>
                    </li>

                    <li class="relative group" x-data="{ open: false }">
                        <a class="flex items-center gap-x-3 py-2 px-3 text-sm text-gray-700 rounded-lg hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:hover:text-neutral-300 dark:focus:bg-neutral-900 dark:focus:text-neutral-300" href="#">
                            Hello World
                            <button @click.prevent.stop="open = !open" class="ml-auto cursor-pointer hidden group-hover:inline-block">
                                <x-icons.dots class="inline-block" />
                            </button>
                        </a>

                        <div x-cloak x-show="open" @click.away="open = false" class="absolute right-[-10px] bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-900 dark:border-neutral-700">
                            <ul class="py-1">
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-800">
                                        <x-icons.edit class="inline-block mr-2 text-gray-500" />
                                        Rename
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-neutral-800">
                                        <x-icons.delete class="inline-block mr-2 text-red-500" />
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                </ul>
                <!-- End List -->
            </div>

            <!-- Footer -->
            <div class="mt-auto">
                <div class="border-t border-gray-200 dark:border-neutral-700">
                    <livewire:model-selector />
                </div>
            </div>
            <!-- End Footer -->
        </nav>
    </div>
    <!-- End Sidebar -->

    <!-- Content -->
    <div class="relative h-screen w-full lg:ps-64">
        <div class="py-1">

            <ul class="space-y-5">

                @if(!ApiKey::hasApiKeys())
                    <li class="mb-5">
                        <livewire:api-key-banner />
                    </li>
                @endif

                <!-- Chat Bubble -->
                <li class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
                    <div class="grow space-y-3">
                        <!-- Card -->
                        <div class="inline-block bg-gray-300 rounded-lg p-4 shadow-sm">
                            <p class="text-sm leading-loose text-gray-950" style="font-size: 98%;">
                                Preline UI is an open-source set of prebuilt UI components based on the utility-first Tailwind CSS framework.
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
                        <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2 dark:bg-neutral-900 dark:border-neutral-700">
                            <p class="text-sm leading-loose text-gray-800 dark:text-white" style="font-size: 98%;">
                                Preline UI is an open-source set of prebuilt UI components based on the utility-first Tailwind CSS framework.
                                Preline UI is an open-source set of prebuilt UI components based on the utility-first Tailwind CSS framework.
                                Preline UI is an open-source set of prebuilt UI components based on the utility-first Tailwind CSS framework.
                            </p>
                        </div>
                        <!-- End Card -->

                        <!-- Button Group -->
                        <div class="sm:flex sm:justify-between">
                            <div class="mt-[-5px]">
                                <button type="button" class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                    <x-icons.copy/>
                                    Copy
                                </button>
                                <button type="button" class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
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
            <div class="flex w-full items-center" x-data="{
        query: '',
        adjustHeight() {
            this.$refs.textarea.style.height = 'auto';
            const lines = this.$refs.textarea.value.split('\n').length;
            const lineHeight = parseInt(window.getComputedStyle(this.$refs.textarea).lineHeight);
            const maxHeight = lineHeight * 5;
            this.$refs.textarea.style.height = Math.min(this.$refs.textarea.scrollHeight, maxHeight) + 'px';
        }
    }">
                <div class="flex w-full flex-col gap-1.5 rounded p-1 transition-colors bg-gray-200 dark:bg-token-main-surface-secondary">
                    <div class="flex items-end gap-1.5 md:gap-2">

                        <div class="flex min-w-0 flex-1 flex-col">
                            <textarea
                                x-ref="textarea"
                                x-model="query"
                                x-on:input="adjustHeight()"
                                x-on:paste="setTimeout(() => adjustHeight(), 0)"
                                tabindex="0"
                                autofocus
                                dir="auto"
                                rows="1"
                                placeholder="Ask me anything..."
                                class="m-0 resize-none border-0 rounded px-4 focus:ring-0 focus-visible:ring-0"
                                style="height: 40px;"></textarea>
                        </div>

                        @if(!ApiKey::hasApiKeys())
                            <button wire:offline.attr="disabled" disabled class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32" class="icon-2xl">
                                    <path fill="currentColor" fill-rule="evenodd" d="M15.192 8.906a1.143 1.143 0 0 1 1.616 0l5.143 5.143a1.143 1.143 0 0 1-1.616 1.616l-3.192-3.192v9.813a1.143 1.143 0 0 1-2.286 0v-9.813l-3.192 3.192a1.143 1.143 0 1 1-1.616-1.616z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        @else
                            <button wire:offline.attr="disabled" :disabled="!query.trim()" class="mb-1 me-1 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors focus-visible:outline-none focus-visible:outline-black disabled:bg-gray-400 disabled:text-[#f4f4f4] disabled:hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 32 32" class="icon-2xl">
                                    <path fill="currentColor" fill-rule="evenodd" d="M15.192 8.906a1.143 1.143 0 0 1 1.616 0l5.143 5.143a1.143 1.143 0 0 1-1.616 1.616l-3.192-3.192v9.813a1.143 1.143 0 0 1-2.286 0v-9.813l-3.192 3.192a1.143 1.143 0 1 1-1.616-1.616z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        @endif


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


