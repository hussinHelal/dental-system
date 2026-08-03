<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Jobs\GenerateBackupJob;
use App\Models\Backup;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BackupController extends Controller
{
    use RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Backup::class);

        $backups = Backup::with('generator')->orderByDesc('generated_at')->paginate(15);

        return view('backups.index', compact('backups'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Backup::class);

        $request->validate([
            'type' => ['required', Rule::in(['pdf', 'excel', 'both', 'database'])],
        ]);

        $type = $request->input('type');

        // Create the row immediately (status: queued) so it shows up in
        // the history right away, then let the queue worker fill it in.
        // Generation runs in the background so this request returns
        // instantly regardless of how much clinic data there is to
        // export - see QUEUE_CONNECTION in .env for what "background"
        // requires in production (a running worker process).
        $backup = Backup::create([
            'filename' => '',
            'path' => '',
            'type' => $type,
            'status' => 'queued',
            'size_bytes' => 0,
            'generated_by' => $request->user()->id,
            'generated_at' => now(),
        ]);

        GenerateBackupJob::dispatch($backup->id, $type);

        return $this->respondSuccess($request, __('messages.backup_queued'), 'backups.index');
    }

    public function download(Backup $backup)
    {
        $this->authorize('download', $backup);

        abort_unless($backup->isCompleted(), 404, __('messages.backup_not_ready'));
        abort_unless(Storage::disk('backups')->exists($backup->path), 404);

        return Storage::disk('backups')->download($backup->path, $backup->filename);
    }

    public function destroy(Request $request, Backup $backup)
    {
        $this->authorize('delete', $backup);

        if ($backup->path) {
            Storage::disk('backups')->delete($backup->path);
        }
        $backup->delete();

        return $this->respondSuccess($request, __('messages.backup_deleted'), 'backups.index');
    }
}
