<div>
    <x-flash/>

    <p class="text-xs text-gray-600 mb-4 font-semibold bg-gray-50 border border-gray-200 p-2 rounded-lg">
        If you use dropbox, google drive, onedrive, etc. you can backup your app's data to your cloud storage.
    </p>

    <fieldset class="border border-gray-300 rounded-lg p-4 mb-4">
        <legend class="text-sm font-medium text-gray-500 mx-3">
            Backup Service Absolute Path
        </legend>

        <div class="w-full">
            <label for="path" class="block text-xs font-medium mb-2 text-gray-700">
                Please enter the full absolute path to your backup folder based on your operating system: See example below:
                <br><br>
                <span class="block mt-2 text-gray-600">
                    <strong>For Windows:</strong> <code>C:/Users/YourName/Dropbox</code> or <code>D:/GoogleDrive/backups</code>
                </span>
                <span class="block mt-1 text-gray-600">
                    <strong>For Linux/Mac:</strong> <code>/home/user/dropbox</code> or <code>/mnt/drive/backups</code>
                </span>
                <br>
            </label>
            <input type="text" id="path"
                   wire:model.defer="path"
                   class="py-3 px-4 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                   placeholder="Enter Path">
        </div>
    </fieldset>

    <div
        class="flex items-center justify-end">
        <x-gradient-button wire:click="save">
            <x-icons.ok class="size-5"/>
            Save
        </x-gradient-button>
    </div>
</div>
