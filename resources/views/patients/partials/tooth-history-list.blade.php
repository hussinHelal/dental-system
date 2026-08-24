@if($toothHistory->isEmpty())
    <p class="text-muted mb-0">{{ __('messages.no_tooth_history') }}</p>
@else
    <div class="tooth-history-list d-flex flex-column gap-3">
        @foreach($toothHistory as $entry)
            <div class="border rounded p-3 small">
                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                    <div>
                        <div class="fw-semibold">{{ __('messages.tooth_event_'.$entry['event_type']) }}</div>
                        <div class="text-muted">
                            {{ __('messages.teeth') }}: {{ $entry['tooth_numbers']->join(', ') }}
                        </div>
                        @if($entry['status'])
                            <div class="text-muted">{{ __('messages.status_'.$entry['status']) }}</div>
                        @endif
                        @if($entry['treatment_name'])
                            <div class="text-muted">{{ $entry['treatment_name'] }}</div>
                        @endif
                    </div>
                    <div class="text-muted text-end">
                        <div>{{ $entry['created_at']?->format('Y-m-d H:i') }}</div>
                        <div>{{ $entry['recorder_name'] ?? __('messages.unknown_user') }}</div>
                    </div>
                </div>
                @if(!empty($entry['notes']))
                    <div class="mt-2 text-secondary">{{ $entry['notes'] }}</div>
                @endif
            </div>
        @endforeach
    </div>
@endif
