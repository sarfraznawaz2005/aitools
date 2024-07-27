@props(['color' => 'currentColor'])

<svg xmlns="http://www.w3.org/2000/svg" class="{{ $attributes->merge(['class' => 'shrink-0 size-5'])->get('class') }}" viewBox="0 0 20 20" fill="{{ $color }}">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
</svg>
