@php($tools = config('tools'))

<!-- Sidebar -->
<div id="hs-application-sidebar"
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full duration-300 transform hidden fixed top-12 bg-white start-0 bottom-0 z-[60] w-64 border-e border-gray-200 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500 dark:bg-neutral-900 dark:border-neutral-700"
     role="dialog" tabindex="-1" aria-label="Sidebar"
     x-data="{
         openDropdown: null,
         activeTab: '{{ $conversation && $conversation->archived ? 'archivedConv' : 'activeConv' }}'
     }"
     x-init="$watch('activeTab', (value) => {
        document.querySelectorAll('[role=tabpanel]').forEach(panel => panel.classList.add('hidden'));
        document.getElementById(value).classList.remove('hidden');
     })">

    <nav class="size-full flex flex-col h-full">
        <div class="h-full overflow-y-auto flex-1">

            <!-- List -->
            <ul class="space-y-0">
                @if(hasApiKeysCreated())
                    <li class="p-3">
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

                <!-- Tabs Navigation -->
                <nav class="relative z-0 flex overflow-hidden p-1 bg-gray-200" aria-label="Tabs" role="tablist"
                     aria-orientation="horizontal">
                    <button type="button"
                            @click.prevent="activeTab = 'activeConv'"
                            :class="{ 'text-gray-600 font-bold bg-white rounded-lg text-opacity-80 active': activeTab === 'activeConv', 'text-gray-500 hover:text-gray-700': activeTab !== 'activeConv' }"
                            class="relative min-w-0 flex-1 py-2 text-sm text-center overflow-hidden focus:z-10 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            aria-selected="true" data-hs-tab="#activeConv"
                            aria-controls="activeConv" role="tab">
                        Active
                    </button>
                    <button type="button"
                            @click.prevent="activeTab = 'archivedConv'"
                            :class="{ 'text-gray-600 font-bold bg-white rounded-lg text-opacity-80 active': activeTab === 'archivedConv', 'text-gray-500 hover:text-gray-700': activeTab !== 'archivedConv' }"
                            class="relative min-w-0 flex-1 py-2 text-sm text-center overflow-hidden focus:z-10 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                            aria-selected="false" data-hs-tab="#archivedConv"
                            aria-controls="archivedConv" role="tab">
                        Archived
                    </button>
                </nav>
                <!-- End Tabs Navigation -->

                <!-- Tab Content -->
                <div class="mt-3">
                    <div id="activeConv" role="tabpanel" aria-labelledby="activeConv" x-show="activeTab === 'activeConv'">
                        <livewire:chat.sidebar-convs :conversation="$conversation" :archived="false"/>
                    </div>
                    <div id="archivedConv" x-cloak role="tabpanel" aria-labelledby="archivedConv" x-show="activeTab === 'archivedConv'">
                        <livewire:chat.sidebar-convs :conversation="$conversation" :archived="true"/>
                    </div>
                </div>
                <!-- End Tab Content -->

            </ul>
            <!-- End List -->
        </div>

        @if (hasApiKeysCreated())
            <div
                class="sticky bottom-0 border-gray-200 bg-gray-200 p-1">
                <livewire:general.model-selector for="{{App\Constants::CHATBUDDY_SELECTED_LLM_KEY}}"/>
            </div>
        @endif

    </nav>
</div>
<!-- End Sidebar -->
