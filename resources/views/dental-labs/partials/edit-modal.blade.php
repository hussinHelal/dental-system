@php $lab = $lab ?? null; @endphp

<div class="modal fade" id="editLabModal{{ $lab->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('dental-labs.update', $lab) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dental_labs.edit_lab') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('dental-labs.partials.fields', ['lab' => $lab])
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
