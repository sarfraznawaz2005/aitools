<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Backup the database to specified location.';

    public function handle(): void
    {
        $source = storage_path('database/database.sqlite');
        $destination = storage_path('database/backup.sqlite');

        if (file_exists($destination)) {
            @unlink($destination);

            copy($source, $destination);

            $this->info('Database backup created successfully.');

            info('Database backup created successfully.');
        }
    }
}
