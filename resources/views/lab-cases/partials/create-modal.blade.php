<div class="modal fade" id="createCaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lab-cases.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dental_labs.add_case') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('lab-cases.partials.fields', ['case' => null, 'labs' => $labs])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('dental_labs.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('dental_labs.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
