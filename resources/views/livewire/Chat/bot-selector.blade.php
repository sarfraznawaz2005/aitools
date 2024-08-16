<div>

    <div class="flex flex-col bg-white border shadow-sm rounded-xl m-auto">
        <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:px-5" style="padding: 16px 16px 14px 16px;">
            <p class="text-sm text-gray-500 font-bold">
                Start New Conversation
            </p>
        </div>
        <div class="p-4 md:p-5 text-center">
            <p class="mt-2 text-gray-500">
                Please choose a bot to start a new conversation.
            </p>

            @foreach($bots as $bot)
                <div class="flex items-center justify-center">
                    <button
                        wire:click="selectBot({{ $bot->id }})"
                        class="m-2 p-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700">
                        {{ $bot->name }}
                    </button>
                </div>
            @endforeach

        </div>
    </div>
</div>
