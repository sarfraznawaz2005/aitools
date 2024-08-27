@php($tools = config('tools'))

<!-- Sidebar -->
<div id="hs-application-sidebar"
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full duration-300 transform hidden fixed top-14 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700"
     role="dialog" tabindex="-1" aria-label="Sidebar" x-data="{ openDropdown: null }">

    <nav class="size-full flex flex-col h-full">

        <div class="h-full overflow-y-auto flex-1">

            <!-- List -->
            <ul class="space-y-0.5">

                @if(hasApiKeysCreated())
                    <li class="p-4">
                        <x-gradient-link class="w-full" href="/{{$tools['chat-buddy']['route']}}" wire:navigate>
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="M12 5v14"/>
                            </svg>
                            New Conversation
                        </x-gradient-link>
                    </li>
                @endif

                <nav class="relative z-0 flex border overflow-hidden" aria-label="Tabs" role="tablist"
                     aria-orientation="horizontal">
                    <button type="button"
                            class="hs-tab-active:border-b-blue-600 hs-tab-active:text-gray-900 relative min-w-0 flex-1 bg-white first:border-s-0 border-s border-b-2 py-2 px-4 text-gray-500 hover:text-gray-700 text-sm font-medium text-center overflow-hidden hover:bg-gray-50 focus:z-10 focus:outline-none focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none active"
                            aria-selected="true" data-hs-tab="#activeConv"
                            aria-controls="activeConv" role="tab">
                        Active
                    </button>
                    <button type="button"
                            class="hs-tab-active:border-b-blue-600 hs-tab-active:text-gray-900 relative min-w-0 flex-1 bg-white first:border-s-0 border-s border-b-2 py-2 px-4 text-gray-500 hover:text-gray-700 text-sm font-medium text-center overflow-hidden hover:bg-gray-50 focus:z-10 focus:outline-none focus:text-blue-600 disabled:opacity-50 disabled:pointer-events-none"
                            aria-selected="false" data-hs-tab="#archivedConv"
                            aria-controls="archivedConv" role="tab">
                        Archived
                    </button>
                </nav>

                <div class="mt-3">
                    <div id="activeConv" role="tabpanel" aria-labelledby="activeConv">
                        <livewire:chat.sidebar-convs :conversation="$conversation" :archived="false"/>
                    </div>
                    <div id="archivedConv" role="tabpanel" aria-labelledby="archivedConv"
                         class="hidden">
                        <livewire:chat.sidebar-convs :conversation="$conversation" :archived="true"/>
                    </div>
                </div>

            </ul>
            <!-- End List -->
        </div>

        @if (hasApiKeysCreated())
            <div
                class="sticky bottom-0 border-gray-200 dark:border-neutral-700 bg-gray-200 dark:bg-neutral-900 p-1">
                <livewire:general.model-selector for="{{App\Constants::CHATBUDDY_SELECTED_LLM_KEY}}"/>
            </div>
        @endif

    </nav>
</div>
<!-- End Sidebar -->
