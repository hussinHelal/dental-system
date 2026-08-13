@extends('layouts.app')

@section('title', __('purchases.title'))

@section('content')
    <style>
        tr.collapse.show, tr.collapsing { display: table-row; }
    </style>

    <div class="container-fluid px-0">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3 shadow-sm p-3 rounded-4 zedan-page-header">
            <div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary-subtle p-2"><i class="bi bi-cart3 text-primary"></i></div><h3 class="mb-0">{{ __('purchases.title') }}</h3></div>
            <form method="GET" class="d-flex flex-wrap gap-2 flex-grow-1 justify-content-end">
                <select name="supplier_id" class="form-select" style="max-width: 220px;">
                    <option value="">{{ __('purchases.all') }}</option>
                    @foreach ($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach
                </select>
                <select name="payment_status" class="form-select" style="max-width: 180px;">
                    <option value="">{{ __('purchases.all') }}</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>{{ __('purchases.paid') }}</option>
                    <option value="partial" @selected(request('payment_status') === 'partial')>{{ __('purchases.partial') }}</option>
                    <option value="unpaid" @selected(request('payment_status') === 'unpaid')>{{ __('purchases.unpaid') }}</option>
                </select>
                <button class="btn btn-outline-primary text-nowrap" type="submit">{{ __('purchases.filter') }}</button>
                @if(request()->filled('supplier_id') || request()->filled('payment_status'))<a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary" title="{{ __('purchases.filter') }}"><i class="bi bi-x-lg"></i></a>@endif
                <button type="button" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#createPurchaseModal"><i class="bi bi-plus-lg me-1"></i>{{ __('purchases.add_purchase') }}</button>
            </form>
        </div>

        <div class="card zedan-card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table zedan-responsive-table mb-0 align-middle">
                        <thead><tr>
                            <th>{{ __('purchases.date') }}</th><th>{{ __('purchases.supplier') }}</th><th>{{ __('purchases.items_count') }}</th><th>{{ __('purchases.total') }}</th><th>{{ __('purchases.payment_status') }}</th><th>{{ __('purchases.recorded_by') }}</th><th class="text-end">{{ __('purchases.actions') }}</th>
                        </tr></thead>
                        <tbody>
                        @forelse ($purchases as $purchase)
                            <tr>
                                <td data-label="{{ __('purchases.date') }}">{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                <td data-label="{{ __('purchases.supplier') }}"><span class="fw-semibold">{{ $purchase->supplier->name }}</span></td>
                                <td data-label="{{ __('purchases.items_count') }}"><button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" data-bs-toggle="collapse" data-bs-target="#purchaseItems{{ $purchase->id }}" aria-expanded="false">{{ $purchase->items->count() }} {{ __('purchases.view_items') }}</button></td>
                                <td data-label="{{ __('purchases.total') }}"><span class="fw-semibold">{{ number_format($purchase->total_amount, 2) }}</span></td>
                                <td data-label="{{ __('purchases.payment_status') }}"><span class="badge bg-{{ $purchase->payment_status->badgeColor() }}">{{ $purchase->payment_status->label() }}</span></td>
                                <td data-label="{{ __('purchases.recorded_by') }}">{{ $purchase->creator?->name ?: '—' }}</td>
                                <td data-label="{{ __('purchases.actions') }}" class="text-end"><div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPurchaseModal{{ $purchase->id }}" title="{{ __('purchases.edit') }}"><i class="bi bi-pencil"></i></button>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('purchases.confirm_delete') }}')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="{{ __('purchases.delete') }}"><i class="bi bi-trash"></i></button></form>
                                </div></td>
                            </tr>
                            <tr class="collapse" id="purchaseItems{{ $purchase->id }}">
                                <td colspan="7" class="bg-body-tertiary">
                                    <div class="p-2 p-md-3"><div class="table-responsive"><table class="table table-sm mb-0 align-middle">
                                        <thead><tr><th>{{ __('purchases.item_name') }}</th><th>{{ __('purchases.quantity') }}</th><th>{{ __('purchases.unit_price') }}</th><th>{{ __('purchases.subtotal') }}</th></tr></thead>
                                        <tbody>@foreach ($purchase->items as $item)<tr><td>{{ $item->item_name }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price, 2) }}</td><td>{{ number_format($item->subtotal, 2) }}</td></tr>@endforeach</tbody>
                                    </table></div></div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5"><div class="text-muted"><i class="bi bi-cart3 fs-2 d-block mb-2"></i>{{ __('purchases.no_records') }}</div></td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($purchases->hasPages())<div class="card-footer bg-transparent border-0 pt-0 pb-3">{{ $purchases->withQueryString()->links('pagination::bootstrap-5') }}</div>@endif
        </div>
    </div>

    @foreach ($purchases as $purchase)
        <div class="modal fade" id="editPurchaseModal{{ $purchase->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
            <form action="{{ route('purchases.update', $purchase) }}" method="POST">@csrf @method('PUT')
                <div class="modal-header"><h5 class="modal-title">{{ __('purchases.edit_purchase') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">@include('purchases.partials.fields', ['purchase' => $purchase, 'suppliers' => $suppliers, 'tableId' => 'itemsTableEdit'.$purchase->id])</div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('purchases.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('purchases.save') }}</button></div>
            </form>
        </div></div></div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @foreach ($purchases as $purchase)
                @foreach ($purchase->items as $item)
                    addPurchaseItemRow('itemsTableEdit{{ $purchase->id }}', @json($item->item_name), {{ $item->quantity }}, {{ $item->unit_price }});
                @endforeach
            @endforeach
        });
    </script>

    <div class="modal fade" id="createPurchaseModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content">
        <form action="{{ route('purchases.store') }}" method="POST">@csrf
            <div class="modal-header"><h5 class="modal-title">{{ __('purchases.add_purchase') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">@include('purchases.partials.fields', ['purchase' => null, 'suppliers' => $suppliers, 'tableId' => 'itemsTableCreate'])</div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('purchases.cancel') }}</button><button type="submit" class="btn btn-primary">{{ __('purchases.save') }}</button></div>
        </form>
    </div></div></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () { addPurchaseItemRow('itemsTableCreate'); });
    </script>
@endsection
