<div class="bg-gray-50 px-8 py-4">
    <div
        class="rounded-lg p-3 my-4 border border-gray-300 w-fit justify-center m-auto bg-gray-200 font-[500] text-gray-800 text-base">
        ⏰ Hey there, this is your reminder for following note!
    </div>

    <div class="prose mx-auto px-6 py-2 bg-white rounded-lg border border-gray-300 shadow-2xl">
        <x-markdown>{!! $note->content !!}</x-markdown>
    </div>
</div>
