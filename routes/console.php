<?php

use App\Constants;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Artisan::command('cleanup', function () {
    $systemFolders = [
        'pak-constitution-bot',
    ];

    Artisan::call('optimize:clear');
    $this->info('Cache cleared successfully!');

    File::cleanDirectory(storage_path('app/livewire-tmp'));
    File::deleteDirectory(storage_path('app/livewire-tmp'));

    $files = glob(storage_path('app') . '/*.json');

    foreach ($files as $file) {
        @unlink($file);
    }

    $files = glob(storage_path('app/files') . '/*');

    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }

    $folders = glob(storage_path('app/files') . '/*', GLOB_ONLYDIR);

    foreach ($folders as $folder) {
        $folderName = basename($folder);

        if (!in_array($folderName, $systemFolders)) {
            File::cleanDirectory($folder);
            File::deleteDirectory($folder);
        }
    }

})->purpose('Clear cache and temp files');


Artisan::command('test', function () {
    $llm = getSelectedLLMProvider(Constants::CHATBUDDY_SELECTED_LLM_KEY);

    $title = $llm->chat(
        'Create only a single title from the text, it should not be more than 25 characters, keep the language spoken: Hello World'
    );

    echo $title;
});
