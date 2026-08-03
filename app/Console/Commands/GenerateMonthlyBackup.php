<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class GenerateMonthlyBackup extends Command
{
    protected $signature = 'backup:generate {--type=both : pdf, excel, or both}';

    protected $description = 'Generate a full clinic data backup (PDF/Excel/both) and record it in the backup history.';

    public function handle(BackupService $backupService): int
    {
        $type = $this->option('type');

        $backup = $backupService->generate($type, null);

        $this->info("Backup created: {$backup->filename} ({$backup->humanSize()})");

        return self::SUCCESS;
    }
}
