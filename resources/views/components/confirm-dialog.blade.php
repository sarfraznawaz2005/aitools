<div
    class="inline"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.away="open = false"
>

    <button
        @click="open = true" {{ $attributes->merge(['class' => 'items-center px-2 py-1 text-white bg-red-600 hover:bg-red-800 rounded']) }}>
        {{ $slot }}
    </button>

    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-[80]">
        <div class="fixed inset-0 bg-black opacity-50 z-[70]" @click="open = false"></div>
        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-lg p-6 w-full max-w-sm mx-auto z-[80]" @click.stop>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-300 mb-4">Confirm Deletion</h2>
            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-6">Are you sure you want to delete this item?</p>
            <div class="flex justify-end gap-4">
                <button @click="open = false"
                        class="py-2 px-4 bg-gray-200 dark:bg-neutral-700 text-gray-800 dark:text-neutral-300 rounded-lg hover:bg-gray-300 dark:hover:bg-neutral-600">
                    Cancel
                </button>
                <button
                    wire:click="{{$using}}({{ $id }})"
                    @click="open = false"
                        class="py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <x-icons.ok class="size-4 inline-block"/> Delete
                </button>
            </div>
        </div>
    </div>
</div>
