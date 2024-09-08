<div wire:init="loadBots">

    <x-modal id="botForwardModal">
        <x-slot name="title">
            <div class="flex gap-x-2">
                Choose Bot
            </div>
        </x-slot>

        <x-slot name="body">

            <div
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4 font-medium">
                @if($loaded)
                    @foreach($bots as $bot)
                        @if(isset($forwarderBot) && $bot->id == $forwarderBot->id)
                            @continue
                        @endif

                        <button
                            type="button"
                            wire:click.prevent="selectBot({{ $bot->id }})"
                            x-data="{
                           isSelected: false,
                           init() {
                                    $wire.on('botChosen', (botId) => {
                                        this.isSelected = (botId == {{ $bot->id }});
                                    });
                                }
                            }"
                            :class="{ 'bg-yellow-100 hover:bg-yellow-100': isSelected }"
                            x-tooltip.raw="{{$bot->bio}}"
                            wire:key="bot-{{ $bot->id }}"
                            x-ref="button"
                            class="w-full text-sm inline-flex items-center rounded-lg border border-gray-300 text-gray-800 hover:bg-gray-200 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                        >

                    <span class="inline-flex justify-center items-center p-1 rounded-lg">
                      <span class="inline-block text-base lg:text-2xl md:text-2xl xl:text-2xl">
                          {{ $bot->icon }}
                      </span>
                    </span>

                            <span class="truncate">{{ $bot->name }}</span>
                        </button>
                    @endforeach
                @endif
            </div>

            <div
                class="flex items-center border-t border-gray-300 justify-end mt-4 pt-4">
                <x-gradient-button wire:click="forward">
                    <x-icons.share class="size-5"/>
                    Forward
                </x-gradient-button>
            </div>

        </x-slot>
    </x-modal>


</div>
