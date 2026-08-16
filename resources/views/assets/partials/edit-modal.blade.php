@php $asset = $asset ?? null; @endphp

<div class="modal fade" id="editAssetModal{{ $asset->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('assets.edit_asset') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @include('assets.partials.fields', ['asset' => $asset])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ __('assets.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('assets.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
