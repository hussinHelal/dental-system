<?php

namespace App\Http\Controllers;

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = isset($filters['search']) ? trim($filters['search']) : null;

        $employees = User::query()
            ->select(['id', 'name', 'username'])
            ->with([
                'profile',
                'roles:id,name',
            ])
            ->when($search !== null && $search !== '', function ($q) use ($search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('username', 'like', '%'.$search.'%');
                });
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        $roles = Role::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, false);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole($data['role']);
            $this->saveProfile($user, $data);
        });

        return back()->with('success', __('employees.created_successfully'));
    }

    public function update(Request $request, User $employee): RedirectResponse
    {
        $data = $this->validated($request, true);

        DB::transaction(function () use ($data, $employee): void {
            $userData = ['name' => $data['name']];

            if (! empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $employee->update($userData);
            $employee->syncRoles([$data['role']]);
            $this->saveProfile($employee, $data);
        });

        return back()->with('success', __('employees.updated_successfully'));
    }

    public function destroy(User $employee): RedirectResponse
    {
        // This intentionally preserves the user row and history. The profile is
        // the module's source of employment status, so deactivation is reversible.
        $employee->profile()->updateOrCreate([], ['status' => 'inactive']);

        return back()->with('success', __('employees.deactivated_successfully'));
    }

    private function validated(Request $request, bool $isUpdate): array
    {
        $usernameRules = $isUpdate
            ? ['sometimes', 'prohibited']
            : ['required', 'string', 'max:100', 'unique:users,username'];

        $passwordRules = $isUpdate
            ? ['nullable', 'string', 'min:8', 'max:255']
            : ['required', 'string', 'min:8', 'max:255'];

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => $usernameRules,
            'password' => $passwordRules,
            'role' => ['required', 'string', 'max:100', 'exists:roles,name'],
            'job_title' => ['required', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date', 'before_or_equal:today'],
            'salary' => ['nullable', 'decimal:0,2', 'numeric', 'min:0', 'max:99999999.99'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,inactive'],
        ]);
    }

    private function saveProfile(User $user, array $data): void
    {
        $user->profile()->updateOrCreate([], [
            'job_title' => $data['job_title'],
            'department' => $data['department'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'hire_date' => $data['hire_date'] ?? null,
            'salary' => $data['salary'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'status' => $data['status'],
        ]);
    }
}
