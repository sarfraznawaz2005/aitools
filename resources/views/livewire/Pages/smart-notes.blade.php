<div>
    <div class="flex">
        <livewire:notes.sidebar/>

        <main class="flex-1 bg-gray-50 border-l">

            @if(!hasApiKeysCreated())
                <div class="mt-20 px-8">
                    <livewire:apikeys.api-key-banner/>
                </div>
            @endif

            <div class="text-center font-medium text-gray-400 text-xl p-2 h-screen items-center flex justify-center">
                <span
                    class="inline-flex items-center gap-x-1.5 py-3 px-6 rounded-full bg-zinc-200 text-gray-500">
                    You have total of {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders
                </span>
            </div>
        </main>
    </div>
</div>
