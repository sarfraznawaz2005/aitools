@props(['type', 'title', 'message', 'buttonText', 'buttonLink' => '#', 'buttonClick' => '', 'buttonAttributes' => ''])

@php
    switch ($type) {
        case 'info':
            $colorStart = 'blue-400';
            $colorEnd = 'blue-600';
            break;
        case 'success':
            $colorStart = 'green-400';
            $colorEnd = 'green-600';
            break;
        case 'warning':
            $colorStart = 'yellow-400';
            $colorEnd = 'yellow-600';
            break;
        default:
            $colorStart = 'gray-400';
            $colorEnd = 'gray-600';
            break;
    }
@endphp

<div class="bg-gradient-to-r from-{{ $colorStart }} to-{{ $colorEnd }} p-4 mb-6 rounded">
    <div class="px-4 mx-auto">
        <div class="grid justify-center md:grid-cols-2 md:justify-between md:items-center gap-2">
            <div class="text-center md:text-start">
                <p class="text-xs text-white uppercase tracking-wider font-bold">
                    {{ $title }}
                </p>
                <p class="mt-3 text-white font-medium">
                    {{ $message }}
                </p>
            </div>

            @if ($buttonText)
                <div class="text-center md:text-start md:flex md:justify-end md:items-center">
                    <a href="{{ $buttonLink }}" @if($buttonClick) onclick="{{ $buttonClick }}" @endif {{ $buttonAttributes }} class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-full border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-200 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-white dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                        {{ $buttonText }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
