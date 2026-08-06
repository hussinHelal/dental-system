@extends('layouts.app')

@section('title', __('messages.activity_log'))

@section('content')
    <h3 class="mb-3">{{ __('messages.activity_log') }}</h3>

    <div class="card zedan-card mb-3 shadow-sm">
        <div class="card-body shadow-sm">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input type="search" name="q" class="form-control" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="causer_id" class="form-select">
                        <option value="">{{ __('messages.all_staff') }}</option>
                        @foreach($staff as $person)
                            <option value="{{ $person->id }}" @selected(request('causer_id') == $person->id)>{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="log_name" class="form-select">
                        <option value="">{{ __('messages.all_modules') }}</option>
                        @foreach($logNames as $logName)
                            <option value="{{ $logName }}" @selected(request('log_name') === $logName)>{{ __('messages.module_'.$logName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="event" class="form-select">
                        <option value="">{{ __('messages.all_actions') }}</option>
                        @foreach(['created', 'updated', 'deleted', 'login', 'logout', 'failed_login', 'blocked_login_deactivated'] as $eventOption)
                            <option value="{{ $eventOption }}" @selected(request('event') === $eventOption)>{{ __('messages.event_'.$eventOption) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">{{ __('messages.date_from') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">{{ __('messages.date_to') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> {{ __('messages.search_placeholder') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @php
                $renderActivityValue = function ($value) {
                    if ($value instanceof \DateTimeInterface) {
                        return $value->format('Y-m-d H:i:s');
                    }

                    if (is_array($value) || is_object($value)) {
                        $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        return $json !== false ? $json : '[]';
                    }

                    return (string) $value;
                };
            @endphp
            @if($activities->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.date') }}</th>
                                <th>{{ __('messages.who') }}</th>
                                <th>{{ __('messages.action') }}</th>
                                <th>{{ __('messages.module') }}</th>
                                <th>{{ __('messages.changes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td data-label="{{ __('messages.date') }}">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                    <td data-label="{{ __('messages.who') }}">{{ $activity->causer?->name ?? __('messages.unknown_user') }}</td>
                                    <td data-label="{{ __('messages.action') }}">
                                        <span class="badge text-bg-{{ in_array($activity->event, ['deleted', 'failed_login', 'blocked_login_deactivated']) ? 'danger' : ($activity->event === 'created' ? 'success' : 'secondary') }}">
                                            {{ __('messages.event_'.$activity->event) }}
                                        </span>
                                    </td>
                                    <td data-label="{{ __('messages.module') }}">{{ $activity->log_name ? __('messages.module_'.$activity->log_name) : '-' }}</td>
                                    <td data-label="{{ __('messages.changes') }}">
                                        @php
                                            $attributes = data_get($activity->properties, 'attributes', []);
                                            $old = data_get($activity->properties, 'old', []);
                                        @endphp
                                        @if(count($attributes))
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#activityDetail{{ $activity->id }}">
                                                {{ __('messages.view') }}
                                            </button>
                                            <x-modal :id="'activityDetail'.$activity->id" :title="__('messages.changes')">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('messages.field') }}</th>
                                                            <th>{{ __('messages.old_value') }}</th>
                                                            <th>{{ __('messages.new_value') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($attributes as $field => $newValue)
                                                            <tr>
                                                                <td>{{ $field }}</td>
                                                                <td class="text-secondary">{{ $renderActivityValue(data_get($old, $field, '-')) }}</td>
                                                                <td>{{ $renderActivityValue($newValue) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </x-modal>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3 ">{{ $activities->links() }}</div>
@endsection
