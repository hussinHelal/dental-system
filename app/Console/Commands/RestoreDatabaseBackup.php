<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RestoreDatabaseBackup extends Command
{
    protected $signature = 'backup:restore {backup_id : The ID of a completed "database" type backup - see the Backups page}';

    protected $description = 'Restore the live database from a database-type backup. Run this with the web server and queue worker both stopped.';

    public function handle(BackupService $backupService): int
    {
        $backup = Backup::find($this->argument('backup_id'));

        if (! $backup) {
            $this->error('No backup found with that ID.');

            return self::FAILURE;
        }

        if ($backup->type !== 'database') {
            $this->error('Only "database" type backups can be restored - PDF/Excel backups are human-readable reports, not restorable snapshots. Generate a "Database" backup from the Backups page first.');

            return self::FAILURE;
        }

        if (! $backup->isCompleted()) {
            $this->error('This backup has not finished generating yet (status: '.$backup->status.').');

            return self::FAILURE;
        }

        if (config('database.default') !== 'sqlite') {
            $this->error('This command only supports the sqlite connection.');

            return self::FAILURE;
        }

        $targetPath = config('database.connections.sqlite.database');

        $this->warn('This will REPLACE the live database with the contents of:');
        $this->line("  {$backup->filename} (generated {$backup->generated_at->format('Y-m-d H:i')})");
        $this->newLine();
        $this->warn('Make sure the web server and queue worker are both stopped before continuing -');
        $this->warn('restoring while either is running risks a file-lock error or a corrupted database.');
        $this->newLine();

        if (! $this->confirm('Continue?')) {
            $this->info('Cancelled - nothing was changed.');

            return self::SUCCESS;
        }

        // Safety copy of the CURRENT state before touching anything, so
        // a mistaken restore is itself recoverable.
        $this->info('Backing up the current database first, just in case...');
        $safetyBackup = $backupService->generate('database');
        $this->info("Safety copy saved as backup #{$safetyBackup->id} ({$safetyBackup->filename}).");

        if (! Storage::disk('backups')->exists($backup->path)) {
            $this->error('The backup file itself is missing from storage/app/backups - cannot restore. The safety copy above was still created.');

            return self::FAILURE;
        }

        $backupContent = Storage::disk('backups')->get($backup->path);
        $written = file_put_contents($targetPath, $backupContent);

        if ($written === false) {
            $this->error('Could not write to the database file - check file permissions, and confirm nothing else has it open.');

            return self::FAILURE;
        }

        $this->info('Database restored successfully.');
        $this->info('Restart the web server and queue worker now.');

        return self::SUCCESS;
    }
}
