<x-layouts.app :title="'Home'">
    <!-- Icon Blocks -->
    <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">

            @foreach(config('tools') as $tool)
                <a href="{{route($tool['route'])}}">
                    <div class="size-full bg-white shadow-lg rounded-lg p-2 dark:bg-neutral-900 text-center hover:bg-gray-200">
                        <div class="inline-flex justify-center items-center">
                            <x-dynamic-component :component="'icons.' . $tool['icon']['name']" :color="$tool['icon']['color']" class="shrink-0 size-16"/>
                        </div>
                        <h3 class="block text-lg font-bold text-gray-800 dark:text-white">{{$tool['name']}}</h3>
                        <p class="text-gray-600 dark:text-neutral-400">{{$tool['description']}}</p>
                    </div>
                </a>
            @endforeach

        </div>
    </div>
    <!-- End Icon Blocks -->
</x-layouts.app>
