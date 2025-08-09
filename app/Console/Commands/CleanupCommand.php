<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class CleanupCommand extends Command
{
    protected $signature = 'cleanup';
    protected $description = 'Clear cache and temp files';

    public function handle()
    {
        $systemFolders = [
            'pak-constitution-bot',
        ];

        File::cleanDirectory(base_path('dist'));
        File::deleteDirectory(base_path('dist'));

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

        // othere config files so users do not get these from my system
        @unlink(storage_path('settings-database-backup-path'));

        $this->info('Cleanup completed successfully.');
    }
}
