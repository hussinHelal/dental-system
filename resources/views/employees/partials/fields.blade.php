@php
    $employee = $employee ?? null;
    $profile = $employee->profile ?? null;
    $currentRole = $employee?->roles->pluck('name')->first();
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.name') }}</label>
        <input type="text" name="name" class="form-control" required value="{{ old('name', $employee->name ?? '') }}">
    </div>

    @unless ($isEdit)
        <div class="col-md-6">
            <label class="form-label">{{ __('employees.username') }}</label>
            <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
        </div>
    @endunless

    <div class="col-md-6">
        <label class="form-label">{{ __('employees.password') }}</label>
        <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}
            placeholder="{{ $isEdit ? __('employees.leave_blank_to_keep') : '' }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('employees.role') }}</label>
        <select name="role" class="form-select" required>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected(old('role', $currentRole ?? '') === $role->name)>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.job_title') }}</label>
        <input type="text" name="job_title" class="form-control" required value="{{ old('job_title', $profile->job_title ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.department') }}</label>
        <input type="text" name="department" class="form-control" value="{{ old('department', $profile->department ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.phone') }}</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $profile->phone ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.address') }}</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $profile->address ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('employees.hire_date') }}</label>
        <input type="date" name="hire_date" class="form-control"
            value="{{ old('hire_date', optional($profile?->hire_date)->format('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('employees.salary') }}</label>
        <input type="number" step="0.01" min="0" name="salary" class="form-control" value="{{ old('salary', $profile->salary ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('employees.national_id') }}</label>
        <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $profile->national_id ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('employees.status') }}</label>
        <select name="status" class="form-select" required>
            <option value="active" @selected(old('status', $profile->status ?? 'active') === 'active')>{{ __('employees.status_active') }}</option>
            <option value="inactive" @selected(old('status', $profile->status ?? '') === 'inactive')>{{ __('employees.status_inactive') }}</option>
        </select>
    </div>
</div>
