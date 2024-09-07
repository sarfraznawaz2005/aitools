<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Backup the database to specified location.';

    public function handle(): void
    {
        $source = base_path('database/database.sqlite');

        if (file_exists(storage_path('settings-database-backup-path'))) {
            $destination = file_get_contents(storage_path('settings-database-backup-path'));

            if ($destination) {
                $destination = rtrim($destination, '/\\') . '/aitools-database-backup.sqlite';

                @unlink($destination);

                copy($source, $destination);

                $this->info('Database backup done successfully.');
                info('Database backup done successfully.');
            }
        }

    }
}
