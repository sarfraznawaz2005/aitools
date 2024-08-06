<div class="py-20 px-8">

    <ul class="space-y-5">

        @if(!App\Models\ApiKey::hasApiKeys())
            <li class="mb-5">
                <livewire:apikeys.api-key-banner/>
            </li>
        @else

            @forelse($messages as $message)
                <div wire:key="{{$message->id}}">

                    @if(!$message->is_ai)
                        <li class="my-4">
                            <div class="max-w-2xl ms-auto flex justify-end gap-x-2 sm:gap-x-4">
                                <div class="inline-block bg-gray-300 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm leading-loose text-gray-950" style="font-size: 99%; line-height: 1.8rem;">
                                        {{$message->body}}
                                    </p>
                                </div>
                            </div>
                            <div class="text-gray-500 flex justify-end text-sm">
                                {{isset($message->created_at) ? $message->created_at->diffForHumans() : ''}}
                            </div>
                        </li>
                    @else
                        <li class="flex gap-x-2 sm:gap-x-4 my-8" x-data="{
                            copied: false,
                            copy () {
                              $clipboard($refs.content.innerText)
                              this.copied = true
                              setTimeout(() => {
                                this.copied = false
                              }, 1000)
                            }
                          }">
                            <div class="grow w-full space-y-3">
                                <!-- Card -->
                                <div
                                    class="bg-white border border-gray-200 rounded-lg p-4 space-y-2 dark:bg-neutral-900 dark:border-neutral-700">
                                    <p class="text-sm leading-loose text-gray-800 dark:text-white"
                                       style="font-size: 99%; line-height: 1.8rem;">
                                        <x-markdown x-ref="content">{!! $message->body !!}</x-markdown>
                                    </p>
                                </div>
                                <!-- End Card -->

                                <!-- Button Group -->
                                <div class="sm:flex sm:justify-between">
                                    <div class="text-gray-500 flex justify-end text-sm mt-[-5px]">
                                        {{isset($message->created_at) ? $message->created_at->diffForHumans() : ''}}
                                    </div>
                                    <div class="mt-[-5px]">
                                        <button type="button"
                                                @click="copy"
                                                class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                            <x-icons.copy/>
                                            <span x-text="copied ? 'Copied' : 'Copy'"></span>
                                        </button>
                                        <button type="button"
                                                class="hover:text-gray-800 px-3 inline-flex items-center gap-x-2 text-sm rounded-full border border-transparent text-gray-500 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                            <x-icons.refresh/>
                                            Regenerate
                                        </button>
                                    </div>
                                </div>
                                <!-- End Button Group -->
                            </div>
                        </li>
                    @endif

                </div>
            @empty

                <div class="fixed inset-0 m-auto w-full lg:left-32 h-64 flex items-center justify-center text-gray-300 text-3xl font-bold">
                    start a fresh conversation!
                </div>

            @endforelse

        @endif

    </ul>
</div>
