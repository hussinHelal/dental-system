<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold">{{ __('employees.title') }}</h2>
    </x-slot>

    <div class="container-fluid py-3">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" class="card card-body border-0 shadow-sm mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="employeeSearch" class="form-label small mb-1">{{ __('employees.search') }}</label>
                    <input id="employeeSearch" type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm" maxlength="100" autocomplete="off">
                </div>
                <div class="col-6 col-md-2">
                    <button class="btn btn-sm btn-outline-primary w-100" type="submit">{{ __('employees.filter') }}</button>
                </div>
                @if(request()->filled('search'))
                    <div class="col-6 col-md-2">
                        <a class="btn btn-sm btn-outline-secondary w-100" href="{{ route('employees.index') }}">{{ __('employees.clear_filter') }}</a>
                    </div>
                @endif
            </div>
        </form>

        <div class="text-end mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                + {{ __('employees.add_employee') }}
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>{{ __('employees.name') }}</th>
                        <th>{{ __('employees.role') }}</th>
                        <th>{{ __('employees.job_title') }}</th>
                        <th>{{ __('employees.department') }}</th>
                        <th>{{ __('employees.phone') }}</th>
                        <th>{{ __('employees.hire_date') }}</th>
                        <th>{{ __('employees.status') }}</th>
                        <th class="text-end">{{ __('employees.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->roles->pluck('name')->join(', ') }}</td>
                            <td>{{ $employee->profile->job_title ?? '-' }}</td>
                            <td>{{ $employee->profile->department ?? '-' }}</td>
                            <td>{{ $employee->profile->phone ?? '-' }}</td>
                            <td>{{ optional($employee->profile?->hire_date)->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ ($employee->profile->status ?? 'active') === 'active' ? 'success' : 'secondary' }}">
                                    {{ __('employees.status_' . ($employee->profile->status ?? 'active')) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editEmployeeModal{{ $employee->id }}">
                                    {{ __('employees.edit') }}
                                </button>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('{{ __('employees.confirm_deactivate') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('employees.deactivate') }}</button>
                                </form>
                            </td>
                        </tr>

                        
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">{{ __('employees.no_records') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @foreach ($employees as $employee)
        <div class="modal fade" id="editEmployeeModal{{ $employee->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('employees.update', $employee) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('employees.edit_employee') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @include('employees.partials.fields', ['employee' => $employee, 'roles' => $roles, 'isEdit' => true])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('employees.cancel') }}</button>
                                                    <button type="submit" class="btn btn-primary">{{ __('employees.save') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
        @endforeach

        {{ $employees->links('pagination::bootstrap-5') }}
    </div>

    <div class="modal fade" id="createEmployeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('employees.add_employee') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('employees.partials.fields', ['employee' => null, 'roles' => $roles, 'isEdit' => false])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('employees.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('employees.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
