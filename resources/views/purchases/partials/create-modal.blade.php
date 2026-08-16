<div class="modal fade" id="createPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('purchases.add_purchase') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('purchases.partials.fields', [
                        'purchase' => null,
                        'suppliers' => $suppliers,
                        'tableId' => 'itemsTableCreate',
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
        addPurchaseItemRow('itemsTableCreate');
    });
</script>
