<div>

    <div class="flex flex-col bg-white border shadow-sm rounded-xl m-auto">
        <div class="bg-gray-100 border-b rounded-t-xl py-3 px-4 md:px-5" style="padding: 16px 16px 14px 16px;">
            <p class="text-sm text-gray-500 font-bold">
                Start New Conversation
            </p>
        </div>
        <div class="p-4 md:p-5 text-center">
            <div
                class="rounded-lg p-3 border border-gray-200 w-fit justify-center m-auto font-medium bg-gray-100 text-gray-500 text-xs sm:text-sm md:text-base lg:text-base mb-4">
                Hey there, pick a bot or start talking to the versatile General bot by default.
            </div>

            <div class="w-full flex justify-center items-center flex-wrap">
                <fieldset
                    class="items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-5 pb-7 dark:border-neutral-700 my-4">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">Your Bots</legend>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 font-medium">
                        @foreach($bots as $bot)
                            @if(!$bot->system)
                                <button
                                    type="button"
                                    wire:click.prevent="selectBot({{ $bot->id }})"
                                    x-data="{
                                    isSelected: false,
                                    init() {
                                            $wire.on('botSelected', (botId) => {
                                                this.isSelected = (botId == {{ $bot->id }});
                                            });
                                        }
                                    }"
                                    :class="{ 'bg-yellow-100': isSelected }"
                                    x-tooltip.raw="{{$bot->bio}}"
                                    wire:key="bot-{{ $bot->id }}"
                                    x-ref="button"
                                    class="w-full py-1 px-2 text-sm inline-flex items-center rounded-lg border border-gray-300 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none"
                                >
                                <span class="inline-block text-base lg:text-2xl md:text-2xl xl:text-2xl mr-1">
                                    {{ $bot->icon }}
                                </span>
                                    {{ $bot->name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </fieldset>

                <fieldset
                    class="items-center justify-center font-semibold w-full border border-gray-300 rounded-lg p-5 pb-7 dark:border-neutral-700 my-4">
                    <legend class="text-sm text-gray-500 dark:text-neutral-300">System Bots</legend>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 font-medium">
                        @foreach($bots as $bot)
                            @if($bot->system)
                                <button
                                    type="button"
                                    wire:click.prevent="selectBot({{ $bot->id }})"
                                    x-data="{
                                   isSelected: false,
                                    init() {
                                            $wire.on('botSelected', (botId) => {
                                                this.isSelected = (botId == {{ $bot->id }});
                                            });
                                        }
                                    }"
                                    :class="{ 'bg-yellow-100': isSelected }"
                                    x-tooltip.raw="{{$bot->bio}}"
                                    wire:key="bot-{{ $bot->id }}"
                                    x-ref="button"
                                    class="w-full py-1 px-2 text-sm inline-flex items-center rounded-lg border border-gray-300 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none"
                                >
                                <span class="inline-block text-base lg:text-2xl md:text-2xl xl:text-2xl mr-1">
                                    {{ $bot->icon }}
                                </span>
                                    {{ $bot->name }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </fieldset>
            </div>

        </div>
    </div>
</div>
