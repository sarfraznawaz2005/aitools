<button {{ $attributes->merge(['class' => 'font-bold flex items-center justify-center gap-x-3 py-2 px-3 text-sm text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600']) }}>
    {{ $slot }}
</button>
