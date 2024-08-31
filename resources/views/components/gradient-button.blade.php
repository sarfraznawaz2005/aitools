<button wire:loading.attr="disabled" {{ $attributes->merge(['class' => 'font-[500] flex items-center justify-center gap-x-1 py-2 px-3 text-sm text-white rounded-lg focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-blue-500 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600']) }}>
    {{ $slot }}
</button>
