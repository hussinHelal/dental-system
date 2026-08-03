@extends('layouts.app')

@section('title', __('messages.treatments'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm">
        <h3 class="mb-0">{{ __('messages.treatments') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end flex-wrap">
            <x-search-bar :placeholder="__('messages.search_treatments')" />
            <form method="GET" class="d-flex gap-2">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </form>
            @can('create', \App\Models\Treatment::class)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTreatmentModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_treatment') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($treatments->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.category') }}</th>
                                <th>{{ __('messages.default_cost') }}</th>
                                <th>{{ __('messages.multi_session') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($treatments as $treatment)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        <a href="{{ route('treatments.show', $treatment) }}">{{ $treatment->name }}</a>
                                    </td>
                                    <td data-label="{{ __('messages.category') }}">{{ $treatment->category }}</td>
                                    <td data-label="{{ __('messages.default_cost') }}">{{ number_format($treatment->default_cost, 2) }}</td>
                                    <td data-label="{{ __('messages.multi_session') }}">
                                        @if($treatment->is_multi_session)
                                            <span class="badge text-bg-info">{{ __('messages.yes') }}</span>
                                        @else
                                            {{ __('messages.no') }}
                                        @endif
                                    </td>
                                    <td data-label="">
                                        @can('update', $treatment)
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTreatmentModal{{ $treatment->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('delete', $treatment)
                                            @if($treatment->is_active)
                                                <form data-ajax-form method="POST" action="{{ route('treatments.destroy', $treatment) }}" class="d-inline" data-confirm="{{ __('messages.confirm_deactivate') }}">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="{{ __('messages.deactivate') }}"><i class="bi bi-slash-circle"></i></button>
                                                </form>
                                            @else
                                                <form data-ajax-form method="POST" action="{{ route('treatments.reactivate', $treatment) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" title="{{ __('messages.activate') }}"><i class="bi bi-check-circle"></i></button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>

                                @can('update', $treatment)
                                    <x-modal :id="'editTreatmentModal'.$treatment->id" :title="__('messages.edit_treatment')">
                                        <form data-ajax-form method="POST" action="{{ route('treatments.update', $treatment) }}">
                                            @csrf @method('PUT')
                                            <x-form-input name="name" :label="__('messages.name')" :value="$treatment->name" required />
                                            <x-form-input name="category" :label="__('messages.category')" :value="$treatment->category" />
                                            <x-form-textarea name="description" :label="__('messages.description')" :value="$treatment->description" />
                                            <x-form-input type="number" name="typical_duration_minutes" :label="__('messages.duration_minutes')" :value="$treatment->typical_duration_minutes" />
                                            <x-form-input type="number" step="0.01" name="default_cost" :label="__('messages.default_cost')" :value="$treatment->default_cost" required />
                                            <div class="form-check mb-3">
                                                <input type="checkbox" name="is_multi_session" value="1" class="form-check-input" id="multiSession{{ $treatment->id }}" @checked($treatment->is_multi_session)>
                                                <label class="form-check-label" for="multiSession{{ $treatment->id }}">{{ __('messages.multi_session') }}</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                                        </form>
                                    </x-modal>
                                @endcan
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $treatments->links() }}</div>

    @can('create', \App\Models\Treatment::class)
        <x-modal id="createTreatmentModal" :title="__('messages.add_treatment')">
            <form data-ajax-form method="POST" action="{{ route('treatments.store') }}">
                @csrf
                <x-form-input name="name" :label="__('messages.name')" required />
                <x-form-input name="category" :label="__('messages.category')" />
                <x-form-textarea name="description" :label="__('messages.description')" />
                <x-form-input type="number" name="typical_duration_minutes" :label="__('messages.duration_minutes')" />
                <x-form-input type="number" step="0.01" name="default_cost" :label="__('messages.default_cost')" required />
                <div class="form-check mb-3">
                    <input type="checkbox" name="is_multi_session" value="1" class="form-check-input" id="multiSessionNew">
                    <label class="form-check-label" for="multiSessionNew">{{ __('messages.multi_session') }}</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection
