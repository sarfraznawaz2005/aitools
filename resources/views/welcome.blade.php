<x-layouts.app :title="''">
    <div class="mx-auto pt-24 px-8">
        <livewire:apikeys.api-key-banner />

        <div class="grid sm:grid-cols-3 lg:grid-cols-3 gap-6 md:gap-10">
            @foreach(config('tools') as $tool)
                <a href="{{route($tool['route'])}}" wire:navigate wire:key="{{ $tool['name'] }}">
                    <div class="size-full bg-white shadow-lg rounded-lg p-4 dark:bg-neutral-900 text-center hover:bg-gray-200">
                        <div class="inline-flex justify-center items-center">
                            <x-dynamic-component :component="'icons.' . $tool['icon']['name']" :color="$tool['icon']['color']" class="shrink-0 w-16 h-16    "/>
                        </div>
                        <h3 class="block text-lg font-bold text-gray-800 dark:text-white">{{$tool['name']}}</h3>
                        <p class="text-gray-600 dark:text-neutral-400">{{$tool['description']}}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
