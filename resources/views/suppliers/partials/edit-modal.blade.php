@php $supplier = $supplier ?? null; @endphp

<div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('suppliers.edit_supplier') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">@include('suppliers.partials.fields', ['supplier' => $supplier])</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('suppliers.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('suppliers.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
