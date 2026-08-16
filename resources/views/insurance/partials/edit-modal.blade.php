@php $contract = $contract ?? null; @endphp

<div class="modal fade" id="editInsuranceModal{{ $contract->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('insurance.update', $contract) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('insurance.edit_contract') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('insurance.partials.fields', ['contract' => $contract])
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
