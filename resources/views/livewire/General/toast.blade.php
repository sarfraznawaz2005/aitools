<div>
    @if ($message)
        @php
            $bgClass = match($type) {
                'info' => 'bg-blue-500',
                'success' => 'bg-green-500',
                'warning' => 'bg-yellow-500',
                'error' => 'bg-red-500',
                default => 'bg-gray-500',
            };
        @endphp

        <div x-cloak x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-5 right-5 {{ $bgClass }} text-white p-3 rounded shadow-lg">
            {{ $message }}
        </div>
    @endif
</div>
