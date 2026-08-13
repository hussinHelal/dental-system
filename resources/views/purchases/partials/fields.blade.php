@php $purchase = $purchase ?? null; @endphp

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('purchases.supplier') }}</label>
        <select name="supplier_id" class="form-select" required>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchase->supplier_id ?? '') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('purchases.date') }}</label>
        <input type="date" name="purchase_date" class="form-control" required
            value="{{ old('purchase_date', optional($purchase?->purchase_date)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('purchases.payment_status') }}</label>
        <select name="payment_status" class="form-select" required>
            <option value="unpaid" @selected(old('payment_status', $purchase->payment_status->value ?? 'unpaid') === 'unpaid')>{{ __('purchases.unpaid') }}</option>
            <option value="partial" @selected(old('payment_status', $purchase->payment_status->value ?? '') === 'partial')>{{ __('purchases.partial') }}</option>
            <option value="paid" @selected(old('payment_status', $purchase->payment_status->value ?? '') === 'paid')>{{ __('purchases.paid') }}</option>
        </select>
    </div>
</div>

<label class="form-label">{{ __('purchases.items') }}</label>
<table class="table table-sm" id="{{ $tableId }}">
    <thead>
        <tr>
            <th>{{ __('purchases.item_name') }}</th>
            <th>{{ __('purchases.quantity') }}</th>
            <th>{{ __('purchases.unit_price') }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<button type="button" class="btn btn-sm btn-outline-secondary mb-3" onclick="addPurchaseItemRow('{{ $tableId }}')">
    + {{ __('purchases.add_item') }}
</button>

<div class="mb-3">
    <label class="form-label">{{ __('purchases.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchase->notes ?? '') }}</textarea>
</div>
