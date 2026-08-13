@php $lab = $lab ?? null; @endphp

<div class="mb-3">
    <label class="form-label">{{ __('dental_labs.name') }}</label>
    <input type="text" name="name" class="form-control" required value="{{ old('name', $lab->name ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('dental_labs.phone') }}</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $lab->phone ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('dental_labs.address') }}</label>
    <input type="text" name="address" class="form-control" value="{{ old('address', $lab->address ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('dental_labs.notes') }}</label>
    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $lab->notes ?? '') }}</textarea>
</div>
