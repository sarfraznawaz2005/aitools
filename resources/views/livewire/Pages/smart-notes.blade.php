<div>

    <div wire:ignore>
        <script src="{{asset('/assets/js/quill/quill.js')}}"></script>
        <link href="{{asset('/assets/js/quill/quill.snow.css')}}" rel="stylesheet">
    </div>

    <div class="flex">
        <livewire:notes.sidebar/>

        <main class="flex-1 bg-gray-50 border-l">

            <livewire:apikeys.api-key-banner/>

            <div class="text-center font-medium text-gray-400 text-xl p-2 h-screen items-center flex justify-center">
                <span
                    class="inline-flex items-center gap-x-1.5 py-3 px-6 rounded-full bg-zinc-200 text-gray-500">
                    You have total of {{ $this->totalNotesCount }} notes in {{ $this->folders->count() }} folders

                    <button
                        type="button"
                        wire:click.prevent="$dispatch('openTextNoteModal')"
                        class="py-2 px-2 inline-flex items-center gap-x-1 pr-4 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                    >
                        <x-icons.plus/>
                        Add Note
                    </button>

                </span>
            </div>
        </main>
    </div>

    <livewire:notes.text-note/>
</div>
