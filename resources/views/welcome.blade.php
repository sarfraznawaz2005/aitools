<x-layouts.app :title="''">
    <div class="flex items-center justify-center min-h-screen mx-auto px-8" tabindex="-1">
        <x-flash/>
        <livewire:apikeys.api-key-banner />

        <div class="grid sm:grid-cols-4 lg:grid-cols-4 gap-6 md:gap-10">
            @foreach(config('tools') as $tool)
                <a href="{{route($tool['route'])}}" wire:navigate.hover>
                    <div class="size-full bg-white shadow text-center hover:shadow-lg transition-shadow rounded-lg p-4">
                        <div class="inline-flex justify-center items-center">
                            <img width="75" height="75" alt="{{$tool['name']}}" src="{{$tool['icon_data']}}">
                        </div>
                        <h3 class="block text-lg font-bold text-gray-500 mb-2 dark:text-white">{{$tool['name']}}</h3>
                        <p class="text-gray-600 text-sm">{{$tool['description']}}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</x-layouts.app>
