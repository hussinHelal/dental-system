@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'step' => null])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }} @if($required)<span class="text-danger">*</span>@endif
    </label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        @if($step) step="{{ $step }}" @endif
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
</div>
