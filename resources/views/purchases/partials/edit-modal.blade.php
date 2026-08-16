@php $purchase = $purchase ?? null; @endphp

<div class="modal fade" id="editPurchaseModal{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('purchases.edit_purchase') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('purchases.partials.fields', [
                        'purchase' => $purchase,
                        'suppliers' => $suppliers,
                        'tableId' => 'itemsTableEdit' . $purchase->id,
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('purchases.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('purchases.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @foreach ($purchase->items as $item)
            addPurchaseItemRow(
                'itemsTableEdit{{ $purchase->id }}',
                @json($item->item_name),
                {{ $item->quantity }},
                {{ $item->unit_price }}
            );
        @endforeach
    });
</script>
