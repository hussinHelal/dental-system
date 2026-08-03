@extends('layouts.app')

@section('title', __('messages.inventory'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2 shadow-sm p-2">
        <h3 class="mb-0">{{ __('messages.inventory') }}</h3>
        <div class="d-flex gap-2 flex-grow-1 justify-content-end">
            <x-search-bar :placeholder="__('messages.search_inventory')" />
            @can('create', \App\Models\InventoryItem::class)
                <button class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createItemModal">
                    <i class="bi bi-plus-lg"></i> {{ __('messages.add_item') }}
                </button>
            @endcan
        </div>
    </div>

    <div class="card zedan-card shadow-sm">
        <div class="card-body p-0 shadow-sm">
            @if($items->isEmpty())
                <x-empty-state />
            @else
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.category') }}</th>
                                <th>{{ __('messages.quantity') }}</th>
                                <th>{{ __('messages.unit') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td data-label="{{ __('messages.name') }}">
                                        {{ $item->name }}
                                        @if($item->isLowStock())
                                            <span class="badge badge-low-stock">{{ __('messages.low_stock') }}</span>
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.category') }}">{{ $item->category }}</td>
                                    <td data-label="{{ __('messages.quantity') }}">{{ $item->quantity }}</td>
                                    <td data-label="{{ __('messages.unit') }}">{{ __('messages.unit_'.$item->unit) }}</td>
                                    <td data-label="">
                                        @if(auth()->user()->isDoctor())
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form data-ajax-form method="POST" action="{{ route('inventory.destroy', $item) }}" class="d-inline" data-confirm="{{ __('messages.confirm_delete') }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quantityModal{{ $item->id }}">
                                                <i class="bi bi-123"></i> {{ __('messages.adjust_quantity') }}
                                            </button>
                                        @endif
                                    </td>
                                </tr>

                                @if(auth()->user()->isDoctor())
                                    <x-modal :id="'editItemModal'.$item->id" :title="__('messages.edit_item')">
                                        <form data-ajax-form method="POST" action="{{ route('inventory.update', $item) }}" enctype="multipart/form-data">
                                            @csrf @method('PUT')
                                            <x-form-input name="name" :label="__('messages.name')" :value="$item->name" required />
                                            <x-form-input name="category" :label="__('messages.category')" :value="$item->category" />
                                            <x-form-input type="number" name="quantity" :label="__('messages.quantity')" :value="$item->quantity" required />
                                            <x-form-select name="unit" :label="__('messages.unit')" :value="$item->unit" required
                                                :options="['box' => __('messages.unit_box'), 'piece' => __('messages.unit_piece'), 'ml' => __('messages.unit_ml')]" />
                                            <x-form-input type="number" name="low_stock_threshold" :label="__('messages.low_stock_threshold')" :value="$item->low_stock_threshold" required />
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.photo') }}</label>
                                                <input type="file" name="photo" class="form-control" accept="image/*">
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                                        </form>
                                    </x-modal>
                                @else
                                    <x-modal :id="'quantityModal'.$item->id" :title="__('messages.adjust_quantity')">
                                        <form data-ajax-form method="POST" action="{{ route('inventory.quantity', $item) }}">
                                            @csrf
                                            <x-form-input type="number" name="quantity" :label="__('messages.quantity')" :value="$item->quantity" required />
                                            <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
                                        </form>
                                    </x-modal>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>

    @can('create', \App\Models\InventoryItem::class)
        <x-modal id="createItemModal" :title="__('messages.add_item')">
            <form data-ajax-form method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data">
                @csrf
                <x-form-input name="name" :label="__('messages.name')" required />
                <x-form-input name="category" :label="__('messages.category')" />
                <x-form-input type="number" name="quantity" :label="__('messages.quantity')" required />
                <x-form-select name="unit" :label="__('messages.unit')" required
                    :options="['box' => __('messages.unit_box'), 'piece' => __('messages.unit_piece'), 'ml' => __('messages.unit_ml')]" />
                <x-form-input type="number" name="low_stock_threshold" :label="__('messages.low_stock_threshold')" required />
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.photo') }}</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.save') }}</button>
            </form>
        </x-modal>
    @endcan
@endsection
