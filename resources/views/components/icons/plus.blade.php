@props(['color' => 'currentColor'])

<svg xmlns="http://www.w3.org/2000/svg" class="{{ $attributes->merge(['class' => 'shrink-0 size-5'])->get('class') }}" viewBox="0 0 20 20" fill="{{ $color }}">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
</svg>
