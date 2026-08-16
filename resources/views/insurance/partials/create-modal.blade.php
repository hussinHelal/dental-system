<div class="modal fade" id="createInsuranceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('insurance.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('insurance.add_contract') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('insurance.partials.fields', ['contract' => null])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('insurance.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('insurance.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
