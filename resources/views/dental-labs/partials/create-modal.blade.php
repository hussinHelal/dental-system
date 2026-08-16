<div class="modal fade" id="createLabModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('dental-labs.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dental_labs.add_lab') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('dental-labs.partials.fields', ['lab' => null])
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
