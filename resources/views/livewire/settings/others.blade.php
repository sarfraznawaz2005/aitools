<div>

    <x-flash/>

    <p class="text-xs text-gray-600 mb-2 text-center font-bold bg-gray-50 border border-gray-100 p-1 rounded">
        📣 These settings take effect after re-starting the app.
    </p>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mb-4">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            Window
        </legend>

        <div class="flex relative mb-4">
            <input type="checkbox"
                   wire:model="alwaysOnTop"
                   class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800"
                   id="hs-default-checkbox">

            <label for="hs-default-checkbox"
                   class="text-sm text-gray-500 ms-3 dark:text-neutral-400">
                Always on Top
            </label>
        </div>

        <div class="flex items-center justify-between space-x-4 mb-4">
            <label for="width"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">Width</label>

            <input type="number" wire:model="width" id="width"
                   min="300" max="1920"
                   class="peer p-2 block w-[8rem] bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600"
            >
        </div>

        <div class="flex items-center justify-between space-x-4 mb-4">
            <label for="height"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height</label>

            <input type="number" wire:model="height" id="height"
                   min="400" max="1080"
                   class="peer p-2 block w-[8rem] bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600"
            >
        </div>

    </fieldset>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mb-4">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            Startup Page
        </legend>

        <div class="relative mb-4">
            <select wire:model.change="page" id="page"
                    autocomplete="off"
                    class="py-2 px-4 pe-9 block w-full font-bold text-center bg-white rounded-lg text-sm outline-0 focus:outline-none focus:ring-0 focus-visible:ring-0 focus:border-gray-400 sm:text-sm md:xs lg:xs">

                <option value="home" class="py-1">Home</option>

                @foreach(config('tools') as $tool)
                    <option
                        class="my-1"
                        value="{{ $tool['route'] }}" {{$page === $tool['route'] ? 'selected' : ''}}>{{ $tool['name'] }}
                    </option>
                @endforeach
            </select>
        </div>

    </fieldset>

    <div
        class="flex items-center justify-between">
        <button wire:click="restore"
                class="font-bold bg-gray-100 border border-gray-200 hover:bg-gray-200 text-gray-600 flex items-center justify-center gap-x-3 py-2 px-3 text-sm rounded-lg focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-blue-500">
            ⬅️ Restore Defaults
        </button>

        <x-gradient-button wire:click="save">
            <x-icons.ok class="size-5"/>
            Save
        </x-gradient-button>
    </div>

</div>
