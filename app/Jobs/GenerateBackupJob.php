<?php

namespace App\Jobs;

use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * A data-heavy clinic's full PDF/Excel export can genuinely take a
     * while - give it real headroom instead of the default 60s, which
     * would kill a large export partway through.
     */
    public int $timeout = 600;

    public int $tries = 1;

    public function __construct(
        public readonly int $backupId,
        public readonly string $type,
    ) {
    }

    public function handle(BackupService $backupService): void
    {
        $backup = Backup::find($this->backupId);

        if (! $backup) {
            // Row was deleted (e.g. manually) before the job ran -
            // nothing left to do.
            return;
        }

        // fillExisting() already marks the row 'failed' internally on
        // exception before re-throwing, so a failed job still leaves a
        // clear, visible status in the UI rather than a row stuck on
        // "queued" forever.
        $backupService->fillExisting($backup, $this->type);
    }

    public function failed(\Throwable $exception): void
    {
        report($exception);

        Backup::whereKey($this->backupId)->update(['status' => 'failed']);
    }
}
