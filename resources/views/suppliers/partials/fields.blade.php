@php $supplier = $supplier ?? null; @endphp

<div class="mb-3">
    <label class="form-label">{{ __('suppliers.name') }}</label>
    <input type="text" name="name" class="form-control" required value="{{ old('name', $supplier->name ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('suppliers.phone') }}</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $supplier->phone ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('suppliers.address') }}</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('suppliers.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplier->notes ?? '') }}</textarea>
</div>
