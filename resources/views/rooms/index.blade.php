@extends('layouts.app')

@section('title', __('messages.rooms'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-2">
        <h3 class="mb-0">{{ __('messages.rooms') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_rooms')" />
            @can('create', \App\Models\Room::class)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoomModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_room') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($rooms->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.equipment_notes') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">{{ $room->name }}</td>
                                    <td data-label="{{ __('messages.equipment_notes') }}">{{ $room->equipment_notes }}</td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="badge text-bg-{{ $room->is_active ? 'success' : 'secondary' }}">
                                            {{ $room->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="">
                                        @can('update', $room)
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRoomModal{{ $room->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endcan
                                        @can('delete', $room)
                                            @if($room->is_active)
                                                <form data-ajax-form method="POST" action="{{ route('rooms.destroy', $room) }}" class="d-inline" data-confirm="{{ __('messages.confirm_deactivate') }}">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="{{ __('messages.deactivate') }}"><i class="bi bi-slash-circle"></i></button>
                                                </form>
                                            @else
                                                <form data-ajax-form method="POST" action="{{ route('rooms.reactivate', $room) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success" title="{{ __('messages.activate') }}"><i class="bi bi-check-circle"></i></button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>

                                @can('update', $room)
                                    <x-modal :id="'editRoomModal'.$room->id" :title="__('messages.edit_room')">
                                        <form data-ajax-form method="POST" action="{{ route('rooms.update', $room) }}">
                                            @csrf @method('PUT')
                                            <x-form-input name="name" :label="__('messages.name')" :value="$room->name" required />
                                            <x-form-textarea name="equipment_notes" :label="__('messages.equipment_notes')" :value="$room->equipment_notes" />
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

    @can('create', \App\Models\Room::class)
        <x-modal id="createRoomModal" :title="__('messages.add_room')">
            <form data-ajax-form method="POST" action="{{ route('rooms.store') }}">
                @csrf
                <x-form-input name="name" :label="__('messages.name')" required />
                <x-form-textarea name="equipment_notes" :label="__('messages.equipment_notes')" />
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection
