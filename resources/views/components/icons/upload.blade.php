@props(['color' => 'currentColor'])

<svg xmlns="http://www.w3.org/2000/svg" class="{{ $attributes->merge(['class' => 'shrink-0 size-5'])->get('class') }}" viewBox="0 0 20 20" fill="{{ $color }}">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
</svg>
