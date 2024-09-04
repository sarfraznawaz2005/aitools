<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Native\Laravel\Facades\Settings;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Backup the database to specified location.';

    public function handle(): void
    {
        $source = storage_path('database/database.sqlite');
        $destination = Settings::get('settings.database-backup-path', '');

        if ($destination) {
            $destination = rtrim($destination, '/\\') . '/aitools-database-backup.sqlite';

            @unlink($destination);

            copy($source, $destination);

            $this->info('Database backup done successfully.');
            info('Database backup done successfully.');
        }
    }
}
