@extends('layouts.app')

@section('title', __('messages.backups'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm rounded-4 border border-body-secondary bg-body p-3">
        <h3 class="mb-0">{{ __('messages.backups') }}</h3>
        <div class="d-flex gap-2 flex-wrap">
            @can('create', \App\Models\Backup::class)
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importBackupModal">
                    <i class="bi bi-file-earmark-arrow-up"></i> {{ __('messages.import_backup') }}
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backupNowModal">
                    <i class="bi bi-cloud-arrow-up"></i> {{ __('messages.backup_now') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($backups->isEmpty())
                <x-empty-state />
            @else
                @if($backups->contains('status', 'queued'))
                    <div class="alert alert-info m-3 mb-0 py-2 small">
                        <i class="bi bi-arrow-repeat"></i> {{ __('messages.backup_queued_hint') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.format') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.size') }}</th>
                                <th>{{ __('messages.generated_by') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td data-label="{{ __('messages.date') }}">{{ $backup->generated_at->format('Y-m-d H:i') }}</td>
                                    <td data-label="{{ __('messages.format') }}">{{ strtoupper($backup->type) }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-{{ $backup->statusBadgeClass() }}">
                                            {{ __('messages.backup_status_'.$backup->status) }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('messages.size') }}">{{ $backup->isCompleted() ? $backup->humanSize() : '-' }}</td>
                                    <td data-label="{{ __('messages.generated_by') }}">{{ $backup->generator?->name ?? __('messages.automatic_monthly') }}</td>
                                    <td data-label="">
                                        @if($backup->isCompleted())
                                            <a href="{{ route('backups.download', $backup) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.download') }}">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if($backup->type === 'database')
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#restoreInfoModal{{ $backup->id }}" title="{{ __('messages.restore') }}">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                        @endif
                                        @can('delete', $backup)
                                            <form data-ajax-form method="POST" action="{{ route('backups.destroy', $backup) }}" class="d-inline" data-confirm="{{ __('messages.confirm_delete') }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="{{ __('messages.delete') }}"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>

                                @if($backup->isCompleted() && $backup->type === 'database')
                                    <x-modal :id="'restoreInfoModal'.$backup->id" :title="__('messages.restore_instructions_title')">
                                        <p>{{ __('messages.restore_instructions_intro') }}</p>
                                        <ol class="small">
                                            <li>{{ __('messages.restore_step_stop') }}</li>
                                            <li>
                                                {{ __('messages.restore_step_run') }}
                                                <pre class="bg-body-secondary p-2 rounded mt-1 mb-1 small user-select-all">php artisan backup:restore {{ $backup->id }}</pre>
                                            </li>
                                            <li>{{ __('messages.restore_step_restart') }}</li>
                                        </ol>
                                        <p class="small text-secondary mb-0">{{ __('messages.restore_step_safety_note') }}</p>
                                    </x-modal>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $backups->links() }}</div>

    @can('create', \App\Models\Backup::class)
        <x-modal id="backupNowModal" :title="__('messages.backup_now')">
            <form data-ajax-form method="POST" action="{{ route('backups.store') }}">
                @csrf
                <x-form-select name="type" :label="__('messages.format')" required
                    :options="['pdf' => 'PDF', 'excel' => 'Excel', 'both' => __('messages.both_zipped'), 'database' => __('messages.database_backup')]" />
                <p class="small text-secondary">{{ __('messages.database_backup_hint') }}</p>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.generate_backup') }}</button>
            </form>
        </x-modal>

        <x-modal id="importBackupModal" :title="__('messages.import_backup')">
            <form data-ajax-form method="POST" action="{{ route('backups.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.backup_file') }}</label>
                    <input type="file" name="backup_file" class="form-control" accept=".sqlite,.zip" required>
                    <div class="form-text">{{ __('messages.import_backup_hint') }}</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.import_backup') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection
