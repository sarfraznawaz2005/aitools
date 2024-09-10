<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';
    protected $description = 'Backup the database to specified location.';

    public function handle(): void
    {
        // works only for Windows
        $source = dirname(base_path(), 4) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'database.sqlite';
        $destinationSource = dirname(base_path(), 4) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'settings-database-backup-path';

        $source = str_ireplace(['local', 'programs'], ['Roaming', ''], $source);
        $destinationSource = str_ireplace(['local', 'programs'], ['Roaming', ''], $destinationSource);

        if (file_exists($destinationSource)) {
            $destination = file_get_contents($destinationSource);

            if ($destination) {
                $destination = rtrim($destination, '/\\') . '/aitools-database-backup.sqlite';

                @unlink($destination);

                $result = copy($source, $destination);

                if ($result) {
                    $this->info('Database backup done successfully.');
                    info('Database backup done successfully.');
                }
            }
        }

    }
}
