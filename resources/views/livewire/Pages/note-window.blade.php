<div class="bg-gray-50 px-8 py-4">

    <div class="flex flex-col md:flex-row justify-center w-full gap-4 mb-4">
        <button wire:click="close"
                class="bg-gray-50 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
            ❌ Close
        </button>
    </div>

    <div
        class="rounded-lg p-3 my-4 border border-gray-200 w-fit justify-center m-auto bg-gray-100 text-gray-800 text-sm">
        ⏰ Hey there, this is your reminder for following note!
    </div>

    <div class="prose mx-auto px-6 py-2 bg-white rounded-lg border border-gray-300 shadow-2xl">
        <x-markdown>{!! $note->content !!}</x-markdown>
    </div>
</div>
