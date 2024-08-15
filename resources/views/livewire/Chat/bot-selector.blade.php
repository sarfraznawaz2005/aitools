<div>
    <div
        class="m-auto w-full lg:left-32 flex items-center justify-center text-gray-300 text-3xl font-bold">
        Start New Conversation
    </div>

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
