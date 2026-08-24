<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Support\ManagedPages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Role & Permission management, built around the fixed page list in
 * App\Support\ManagedPages rather than free-text permission names. Every
 * role's permission set is now just "which pages, view or manage" —
 * there's no longer a way to create an arbitrary permission from this UI.
 *
 * IMPORTANT: "manage roles" (the permission that gates this whole
 * controller) is NOT one of the page checkboxes — Role Management isn't
 * in the sidebar list the checkboxes are built from. It's granted only
 * via the seed migration (to Admin Doctor). Because of that, every save
 * here must explicitly PRESERVE "manage roles" on a role that already has
 * it — StoreRoleRequest::toPermissionNames() only ever returns page
 * permissions, so naively syncing to that list alone would silently strip
 * "manage roles" from any role that holds it the moment someone edits
 * that role's page checkboxes. See mergeWithPreservedManageRoles() below.
 *
 * PERMISSION-GATE MECHANICS — READ BEFORE TOUCHING:
 * As of Laravel 11+ (this app is on 13), the base Controller class no
 * longer extends anything that provides an instance-level $this->middleware()
 * method — that pattern is gone. Controller middleware is now declared via
 * the HasMiddleware interface with a STATIC middleware() method (see
 * below), which Laravel's router reads directly off the class without
 * ever instantiating it. Using the old $this->middleware(...) instance
 * pattern here throws "Call to undefined method ...::middleware()" the
 * moment the route resolves — and simply commenting that constructor out
 * (rather than converting it to the interface below) removes the ONLY
 * thing enforcing "manage roles" on this whole controller, silently
 * turning every role-management action open to any logged-in user.
 */
class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:manage roles'),
        ];
    }

    public function index(): View
    {
        $roles = Role::withCount('users')
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $pages = ManagedPages::all();

        return view('roles.create', compact('pages'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => trim($request->validated('name')),
                'guard_name' => 'web',
                'is_locked' => false,
            ]);

            $role->syncPermissions($request->toPermissionNames());
        });

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $this->guardAgainstLockedRole($role);

        $pages = ManagedPages::all();
        $role->load('permissions:id,name');

        // Pre-check the grid from the role's current permissions.
        $currentPermissionNames = $role->permissions->pluck('name')->all();
        $checkedPages = [];
        foreach (ManagedPages::slugs() as $slug) {
            $checkedPages[$slug] = [
                'view' => in_array(ManagedPages::viewPermission($slug), $currentPermissionNames, true),
                'manage' => in_array(ManagedPages::managePermission($slug), $currentPermissionNames, true),
            ];
        }

        return view('roles.edit', compact('role', 'pages', 'checkedPages'));
    }

    public function update(StoreRoleRequest $request, Role $role): RedirectResponse
    {
        $this->guardAgainstLockedRole($role);

        DB::transaction(function () use ($request, $role) {
            $role->update(['name' => trim($request->validated('name'))]);

            $role->syncPermissions(
                $this->mergeWithPreservedManageRoles($role, $request->toPermissionNames())
            );
        });

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->guardAgainstLockedRole($role);

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return back()->with(
                'error',
                "Cannot delete \"{$role->name}\" — it is still assigned to {$userCount} user(s). Reassign them first."
            );
        }

        if ($role->hasPermissionTo('manage roles')) {
            $otherRolesWithManageRoles = Role::whereHas('permissions', function ($query) {
                $query->where('permissions.name', 'manage roles');
            })->where('id', '!=', $role->id)->count();

            if ($otherRolesWithManageRoles === 0) {
                return back()->with(
                    'error',
                    "Cannot delete \"{$role->name}\" — it is the only role that can manage roles. Grant \"manage roles\" to another role first."
                );
            }
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * If $role currently holds "manage roles", keep it in the new
     * permission set being saved — the page checkbox grid has no way to
     * grant or revoke this permission, so an update() here must never be
     * the thing that silently removes it. StoreRoleRequest's own
     * validator already blocks the save entirely if this role is the
     * LAST holder of "manage roles"; this merge is what makes that
     * guarantee actually hold at the data layer, not just at validation
     * time — the two need to agree, or the validator's promise ("we
     * checked this won't happen") would be broken by the save that
     * follows it.
     */
    private function mergeWithPreservedManageRoles(Role $role, array $newPermissionNames): array
    {
        if ($role->hasPermissionTo('manage roles') && ! in_array('manage roles', $newPermissionNames, true)) {
            $newPermissionNames[] = 'manage roles';
        }

        return $newPermissionNames;
    }

    private function guardAgainstLockedRole(Role $role): void
    {
        abort_if(
            $role->is_locked,
            403,
            "The \"{$role->name}\" role is protected and cannot be modified or deleted."
        );
    }
}