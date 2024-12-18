<div>

    <x-flash/>

    <fieldset class="border border-gray-300 rounded-lg p-4">
        <legend class="text-xs font-medium text-gray-500 mx-3">
            Automatically Delete Old AI-Generated Tips (Except Favorited)
        </legend>

        <div class="flex items-center justify-between space-x-4">
            <label for="deleteOldDays"
                   class="block text-sm text-gray-700 dark:text-gray-300">Days</label>

            <input type="number" wire:model="deleteOldDays" id="deleteOldDays"
                   min="1" max="365"
                   class="peer p-2 block w-[8rem] bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600"
            >
        </div>
        <div
            class="flex justify-end items-center gap-x-4 mt-4">
            <x-gradient-button wire:click="saveOptions">
                <x-icons.ok class="size-5"/>
                Save
            </x-gradient-button>
        </div>
    </fieldset>

    <fieldset class="border border-gray-300 rounded-lg p-2 mt-8">
        <legend class="text-xs font-medium text-gray-500 mx-3">
            Delete All AI-Generated Tip Contents (Except Favorited)
        </legend>

        <div class="flex justify-center mt-4">
            <x-confirm-dialog call="deleteAll"
                              text="Are you sure you want to delete all?"
                              class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:bg-red-600 disabled:opacity-50 disabled:pointer-events-none">
                <x-icons.delete class="inline-block mt-[-3px]"/>
                Delete All
            </x-confirm-dialog>
        </div>

    </fieldset>
</div>
