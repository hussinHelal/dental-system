<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('finance.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('finance.add_transaction') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('finance.partials.fields', ['transaction' => null])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('finance.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('finance.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
