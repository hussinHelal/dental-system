<?php

namespace App\Services;

use App\Exports\FullBackupExport;
use App\Models\Appointment;
use App\Models\Backup;
use App\Models\Doctor;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Treatment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class BackupService
{
    /**
     * Synchronous path - used by the scheduled monthly command, which
     * already runs outside any web request so blocking is harmless.
     * Builds the file(s) and creates the Backup row in one step.
     */
    public function generate(string $type, ?int $userId = null): Backup
    {
        $attributes = $this->build($type);

        $backup = Backup::create([
            'filename' => $attributes['filename'],
            'path' => $attributes['path'],
            'type' => $type,
            'status' => 'completed',
            'size_bytes' => $attributes['size_bytes'],
            'generated_by' => $userId,
            'generated_at' => now(),
        ]);

        $this->pruneOlderThanTwelveMonths();

        return $backup;
    }

    /**
     * Async path - used by GenerateBackupJob. $backup already exists as
     * a 'queued' placeholder row (created immediately when the user
     * clicks "Backup Now", so the UI has something to show right away);
     * this fills it in once the file is actually ready, or marks it
     * failed without leaving a stuck "queued" row forever.
     */
    public function fillExisting(Backup $backup, string $type): void
    {
        try {
            $attributes = $this->build($type);
        } catch (\Throwable $e) {
            $backup->update(['status' => 'failed']);

            throw $e;
        }

        $backup->update([
            'filename' => $attributes['filename'],
            'path' => $attributes['path'],
            'size_bytes' => $attributes['size_bytes'],
            'status' => 'completed',
        ]);

        $this->pruneOlderThanTwelveMonths();
    }

    /**
     * Does the actual PDF/Excel/zip generation and returns the resulting
     * file's attributes. Cleans up any partially-written files on
     * failure rather than leaving orphans, and re-throws so the caller
     * (either generate() or fillExisting()) can react.
     *
     * @return array{filename: string, path: string, size_bytes: int}
     */
    private function build(string $type): array
    {
        // Raw database file copy - short-circuits before touching
        // collectModules()/PDF/Excel entirely, since this is the one
        // backup type that can actually restore everything, not just
        // report on it.
        if ($type === 'database') {
            return $this->buildDatabaseCopy();
        }

        $modules = $this->collectModules();
        $monthDir = now()->format('Y-m');
        $stamp = now()->format('Y-m-d_His');

        Storage::disk('backups')->makeDirectory($monthDir);

        $pdfRelativePath = null;
        $excelRelativePath = null;

        try {
            if (in_array($type, ['pdf', 'both'], true)) {
                $pdfRelativePath = "{$monthDir}/zedan-backup-{$stamp}.pdf";
                $pdf = Pdf::loadView('backups.pdf.full', [
                    'modules' => $modules,
                    'generatedAt' => now(),
                ])->setPaper('a4');

                Storage::disk('backups')->put($pdfRelativePath, $pdf->output());
            }

            if (in_array($type, ['excel', 'both'], true)) {
                $excelRelativePath = "{$monthDir}/zedan-backup-{$stamp}.xlsx";
                Excel::store(
                    new FullBackupExport($modules),
                    $excelRelativePath,
                    'backups'
                );
            }

            if ($type === 'both') {
                $finalRelativePath = "{$monthDir}/zedan-backup-{$stamp}.zip";
                $zipFullPath = Storage::disk('backups')->path($finalRelativePath);

                $zip = new ZipArchive;
                $openResult = $zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                if ($openResult !== true) {
                    throw new \RuntimeException("Could not create backup archive (ZipArchive error code {$openResult}).");
                }

                $zip->addFile(Storage::disk('backups')->path($pdfRelativePath), basename($pdfRelativePath));
                $zip->addFile(Storage::disk('backups')->path($excelRelativePath), basename($excelRelativePath));
                $zip->close();

                // Remove the loose files now that they're archived together.
                Storage::disk('backups')->delete([$pdfRelativePath, $excelRelativePath]);

                $finalPath = $finalRelativePath;
            } else {
                $finalPath = $pdfRelativePath ?? $excelRelativePath;
            }
        } catch (\Throwable $e) {
            // Don't leave partially-written files behind on failure.
            foreach ([$pdfRelativePath, $excelRelativePath] as $path) {
                if ($path && Storage::disk('backups')->exists($path)) {
                    Storage::disk('backups')->delete($path);
                }
            }

            throw $e;
        }

        return [
            'filename' => basename($finalPath),
            'path' => $finalPath,
            'size_bytes' => Storage::disk('backups')->size($finalPath),
        ];
    }

    /**
     * @return array{filename: string, path: string, size_bytes: int}
     */
    private function buildDatabaseCopy(): array
    {
        if (config('database.default') !== 'sqlite') {
            throw new \RuntimeException('Database-file backups are only supported on the sqlite connection - use your database server\'s own backup tooling (e.g. mysqldump) on MySQL.');
        }

        $sourcePath = config('database.connections.sqlite.database');

        if (! is_string($sourcePath) || ! file_exists($sourcePath)) {
            throw new \RuntimeException('Could not locate the live SQLite database file to back up.');
        }

        $monthDir = now()->format('Y-m');
        $stamp = now()->format('Y-m-d_His');
        $relativePath = "{$monthDir}/zedan-database-{$stamp}.sqlite";

        Storage::disk('backups')->makeDirectory($monthDir);

        // Plain file_get_contents/put rather than SQLite's own backup
        // API - simple, and SQLite's WAL mode (see config/database.php)
        // means reads aren't blocked by this while the app keeps running.
        Storage::disk('backups')->put($relativePath, file_get_contents($sourcePath));

        return [
            'filename' => basename($relativePath),
            'path' => $relativePath,
            'size_bytes' => Storage::disk('backups')->size($relativePath),
        ];
    }

    /**
     * Keeps the last 12 months of backups, deleting both the files and
     * their history rows for anything older.
     */
    public function pruneOlderThanTwelveMonths(): void
    {
        $cutoff = now()->subMonths(12);

        Backup::where('generated_at', '<', $cutoff)->each(function (Backup $backup) {
            Storage::disk('backups')->delete($backup->path);
            $backup->delete();
        });
    }

    /**
     * @return array<string, array{headings: array, rows: array}>
     */
    private function collectModules(): array
    {
        // cursor() streams rows one at a time instead of loading the
        // whole table into memory - keeps a backup of years of clinic
        // data from becoming a memory-exhaustion risk as it grows.
        return [
            'Doctors' => [
                'headings' => ['ID', 'Name', 'Specialty', 'Phone', 'Active'],
                'rows' => Doctor::cursor()->map(fn ($d) => [
                    $d->id, $d->name, $d->specialty, $d->phone, $d->is_active ? 'Yes' : 'No',
                ])->all(),
            ],
            'Rooms' => [
                'headings' => ['ID', 'Name', 'Equipment Notes', 'Active'],
                'rows' => Room::cursor()->map(fn ($r) => [
                    $r->id, $r->name, $r->equipment_notes, $r->is_active ? 'Yes' : 'No',
                ])->all(),
            ],
            'Patients' => [
                'headings' => ['ID', 'Full Name', 'Phone', 'Gender', 'Address'],
                'rows' => Patient::cursor()->map(fn ($p) => [
                    $p->id, $p->full_name, $p->phone, $p->gender, $p->address,
                ])->all(),
            ],
            'Treatments' => [
                'headings' => ['ID', 'Name', 'Category', 'Default Cost', 'Multi-Session'],
                'rows' => Treatment::cursor()->map(fn ($t) => [
                    $t->id, $t->name, $t->category, $t->default_cost, $t->is_multi_session ? 'Yes' : 'No',
                ])->all(),
            ],
            'Appointments' => [
                'headings' => ['ID', 'Patient', 'Doctor', 'Room', 'Date', 'Start', 'End', 'Status'],
                'rows' => Appointment::with(['patient', 'doctor', 'room'])->cursor()->map(fn ($a) => [
                    $a->id, $a->patient?->full_name, $a->doctor?->name, $a->room?->name,
                    optional($a->appointment_date)->toDateString(), $a->start_time, $a->end_time, $a->status,
                ])->all(),
            ],
            'Payments' => [
                'headings' => ['ID', 'Patient', 'Treatment', 'Type', 'Total', 'Paid', 'Remaining', 'Status'],
                'rows' => Payment::with(['patient', 'treatment'])->cursor()->map(fn ($p) => [
                    $p->id, $p->patient?->full_name, $p->treatment?->name, $p->payment_type,
                    $p->total_amount, $p->amount_paid, $p->remaining_balance, $p->status,
                ])->all(),
            ],
            'Inventory' => [
                'headings' => ['ID', 'Name', 'Quantity', 'Unit', 'Category', 'Low Stock Threshold'],
                'rows' => InventoryItem::cursor()->map(fn ($i) => [
                    $i->id, $i->name, $i->quantity, $i->unit, $i->category, $i->low_stock_threshold,
                ])->all(),
            ],
        ];
    }
}
