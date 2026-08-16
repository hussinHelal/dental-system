@php $case = $case ?? null; @endphp

<div class="modal fade" id="editCaseModal{{ $case->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('lab-cases.update', $case) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dental_labs.edit_case') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('lab-cases.partials.fields', ['case' => $case, 'labs' => $labs])
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
