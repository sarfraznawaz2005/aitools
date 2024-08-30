<x-layouts.app :title="''">
    <div class="mx-auto pt-24 px-8" tabindex="-1">
        <x-flash/>
        <livewire:apikeys.api-key-banner />

        <div class="grid sm:grid-cols-3 lg:grid-cols-3 gap-6 md:gap-10" tabindex="-1">
            @foreach(config('tools') as $tool)
                <a href="{{route($tool['route'])}}" wire:navigate wire:key="{{ $tool['name'] }}" tabindex="-1">
                    <div class="size-full bg-white shadow text-center hover:shadow-lg transition-shadow rounded-lg p-4">
                        <div class="inline-flex justify-center items-center">
                            <img width="75" height="75" alt="{{$tool['name']}}" src="{{$tool['icon_data']}}">
                        </div>
                        <h3 class="block text-lg font-bold text-gray-500 mb-2 dark:text-white">{{$tool['name']}}</h3>
                        <p class="text-gray-600">{{$tool['description']}}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
