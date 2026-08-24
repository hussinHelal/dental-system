{{--
    Shared form for both Create Role and Edit Role.
    Expects: $pages (slug => translation-key-suffix, from ManagedPages::all()),
             optional $role, optional $checkedPages (slug => ['view' => bool, 'manage' => bool]).
--}}
@php
    $checked = $checkedPages ?? [];
@endphp

<div class="mb-3">
    <label for="name" class="form-label">{{ __('Role Name') }}</label>
    <input type="text"
           id="name"
           name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $role->name ?? '') }}"
           maxlength="50"
           required
           autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label d-block">{{ __('Page Access') }}</label>
    <p class="text-muted small mb-2">
        {{ __('Choose whether this role can view each page, or view and manage it. Manage automatically includes View.') }}
    </p>

    <div class="table-responsive border rounded @error('pages') is-invalid @enderror">
        <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>{{ __('Page') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('View') }}</th>
                    <th class="text-center" style="width: 100px;">{{ __('Manage') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $slug => $labelKey)
                    @php
                        $oldView = old("pages.$slug.view");
                        $oldManage = old("pages.$slug.manage");
                        $isViewChecked = $oldView !== null ? (bool) $oldView : ($checked[$slug]['view'] ?? false);
                        $isManageChecked = $oldManage !== null ? (bool) $oldManage : ($checked[$slug]['manage'] ?? false);
                    @endphp
                    <tr>
                        <td>{{ __('messages.page_'.$labelKey) }}</td>
                        <td class="text-center" style="cursor: pointer;" onclick="document.getElementById('view_{{ $slug }}').click()">
                            <input type="checkbox"
                                   id="view_{{ $slug }}"
                                   class="form-check-input page-view-checkbox"
                                   name="pages[{{ $slug }}][view]"
                                   value="1"
                                   data-slug="{{ $slug }}"
                                   {{ $isViewChecked ? 'checked' : '' }}
                                   onclick="event.stopPropagation()">
                        </td>
                        <td class="text-center" style="cursor: pointer;" onclick="document.getElementById('manage_{{ $slug }}').click()">
                            <input type="checkbox"
                                   id="manage_{{ $slug }}"
                                   class="form-check-input page-manage-checkbox"
                                   name="pages[{{ $slug }}][manage]"
                                   value="1"
                                   data-slug="{{ $slug }}"
                                   {{ $isManageChecked ? 'checked' : '' }}
                                   onclick="event.stopPropagation()">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @error('pages')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
(function () {
    // "Manage implies View": checking Manage auto-checks View for the
    // same page. Unchecking View auto-unchecks Manage.
    // The backend authoritatively enforces this in StoreRoleRequest::toPermissionNames().
    document.querySelectorAll('.page-manage-checkbox').forEach(function (manageBox) {
        var slug = manageBox.dataset.slug;
        var viewBox = document.querySelector('.page-view-checkbox[data-slug="' + slug + '"]');
        if (!viewBox) return;

        manageBox.addEventListener('change', function () {
            if (manageBox.checked) {
                viewBox.checked = true;
            }
        });

        viewBox.addEventListener('change', function () {
            if (!viewBox.checked) {
                manageBox.checked = false;
            }
        });

        // Initialize state on load
        if (manageBox.checked) {
            viewBox.checked = true;
        }
    });
})();
</script>
@endpush
