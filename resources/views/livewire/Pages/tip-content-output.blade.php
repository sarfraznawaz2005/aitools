<div class="px-8 py-4 pb bg-white">
    <div class="flex flex-col md:flex-row justify-center w-full gap-4 mb-4">
        <button wire:click="favorite" class="bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
            ⭐ Favorite
        </button>
        <button wire:click="delete" class="bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
            🗑️ Delete
        </button>
        <button wire:click="close" class="bg-gray-100 hover:bg-gray-200 text-gray-600 border border-gray-200 font-medium py-2 px-4 rounded">
            ❌ Close
        </button>
    </div>

    <div class="prose mx-auto bg-gray-100 px-6 py-2 rounded-lg border border-gray-300 shadow-2xl">
        <x-markdown>{!! $tipContent->content !!}</x-markdown>
    </div>
</div>
