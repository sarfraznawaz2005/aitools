<div>

    <x-flash/>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            Automatically Delete Old Conversations (Except Favorited)
        </legend>

        <div class="flex items-center justify-between space-x-4">
            <label for="chatBuddyDeleteOldDays"
                   class="block text-sm font-medium text-gray-700 dark:text-gray-300">Days</label>

            <input type="number" wire:model="chatBuddyDeleteOldDays" id="chatBuddyDeleteOldDays"
                   min="1" max="365"
                   class="peer p-2 block w-[8rem] bg-gray-100 border-transparent rounded-lg text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600"
            >
        </div>
        <div
            class="flex justify-end items-center gap-x-4 pt-4 border-t-2 border-gray-100 dark:border-neutral-700 mt-4">
            <x-gradient-button wire:click="saveChatBuddyOptions">
                <x-icons.ok class="size-5"/>
                Save
            </x-gradient-button>
        </div>
    </fieldset>

    <fieldset class="border border-gray-300 rounded-lg p-4 dark:border-neutral-700 mt-8">
        <legend class="text-sm font-medium text-gray-500 dark:text-neutral-300">
            Delete All Conversations (Except Favorited)
        </legend>

        <div class="flex justify-center mt-4">
            <x-confirm-dialog id="0" using="deleteAllConversations"
                              text="Are you sure you want to delete all conversations?"
                              class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-500 text-white hover:bg-red-600 focus:outline-none focus:bg-red-600 disabled:opacity-50 disabled:pointer-events-none">
                <x-icons.delete class="inline-block mt-[-3px]"/>
                Delete All Conversations
            </x-confirm-dialog>
        </div>

    </fieldset>
</div>
