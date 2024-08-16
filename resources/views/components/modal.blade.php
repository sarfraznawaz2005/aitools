@php
$id = $id ?? 'general-modal';
@endphp

<div id="{{$id}}" wire:ignore.self class="hs-overlay hs-overlay-backdrop-open:bg-blue-950/90 hidden size-full fixed top-0 start-0 z-[100] overflow-x-hidden overflow-y-auto pointer-events-none dark:hs-overlay-backdrop-open:bg-blue-950/90" role="dialog" tabindex="-1" aria-labelledby="hs-custom-backdrop-label">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-3 px-4 border-b dark:border-neutral-700">
                <h3 id="hs-custom-backdrop-label" class="font-bold text-gray-500 dark:text-white">
                    {{ $title }}
                </h3>
                <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close" data-hs-overlay="#{{$id}}">
                    <span class="sr-only">Close</span>
                    <x-icons.close />
                </button>
            </div>
            <div class="p-4 overflow-y-auto">
                {{ $body }}
            </div>
        </div>
    </div>
</div>
