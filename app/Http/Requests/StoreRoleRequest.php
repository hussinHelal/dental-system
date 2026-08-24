<?php

namespace App\Http\Requests;

use App\Support\ManagedPages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Validates role create/update from the page-based permission matrix.
 *
 * Expected payload shape from the form:
 *   name: "Assistant"
 *   pages: {
 *     appointments: { view: "1", manage: "1" },
 *     patients:     { view: "1" },              // view only, no manage
 *     rooms:        { },                        // neither checked, key can be omitted entirely
 *     ...
 *   }
 *
 * There is no free-text "create a new permission" input anymore — the
 * permission set is fixed to whatever App\Support\ManagedPages declares,
 * so the whole form is just checkboxes against a known list. This
 * replaces the earlier version of this class, which accepted arbitrary
 * permission_ids/new_permissions.
 */
class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage roles') ?? false;
    }

    public function rules(): array
    {
        $uniqueRule = Rule::unique('roles', 'name');
        $routeRole = $this->route('role');
        if ($routeRole) {
            $uniqueRule->ignore($routeRole->id);
        }

        return [
            'name' => ['required', 'string', 'min:3', 'max:50', $uniqueRule],

            'pages' => ['nullable', 'array'],
            // Array VALUE shape (view/manage booleans) is validated here.
            // Array KEYS (the page slugs themselves) are checked against
            // ManagedPages::slugs() in withValidator() below — Laravel's
            // rule syntax has no clean way to whitelist array keys inline.
            'pages.*' => ['array'],
            'pages.*.view' => ['nullable', 'boolean'],
            'pages.*.manage' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Two checks here:
     *
     * 1. At least one page permission (view or manage, on at least one
     *    page) must be selected — an empty role is a footgun, same
     *    reasoning as the earlier free-text version of this form.
     *
     * 2. Every key under `pages` must be a real page slug from
     *    ManagedPages — Laravel's validator doesn't have a clean built-in
     *    way to validate array KEYS against a whitelist (only values), so
     *    that check happens here.
     *
     * 3. System-lockout guard carried over from the previous version:
     *    block any save that would leave zero roles able to manage roles.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $pages = $this->input('pages', []);
            $validSlugs = ManagedPages::slugs();

            $unknownSlugs = array_diff(array_keys($pages), $validSlugs);
            if (! empty($unknownSlugs)) {
                $validator->errors()->add('pages', 'One or more selected pages are not recognized.');

                return;
            }

            $hasAnyPagePermission = collect($pages)->contains(
                fn ($flags) => ! empty($flags['view']) || ! empty($flags['manage'])
            );

            if (! $hasAnyPagePermission) {
                $validator->errors()->add('pages', 'Select at least one page for this role to access.');

                return;
            }

            $this->guardAgainstOrphaningRoleManagement($validator);
        });
    }

    private function guardAgainstOrphaningRoleManagement($validator): void
    {
        $manageRolesPermission = Permission::where('name', 'manage roles')->first();
        if (! $manageRolesPermission) {
            return;
        }

        // "manage roles" itself isn't one of the page checkboxes (Role
        // Management isn't in the sidebar list) — it's granted separately,
        // the same way the seeded Admin Doctor role already has it. This
        // guard only matters when editing a role that currently holds it
        // via some other means; nothing on this form can grant it, so this
        // is purely a safety net against an existing "manage roles"
        // holder having that permission taken away some other way in the
        // future. Left in place defensively.
        $routeRole = $this->route('role');
        if (! $routeRole || ! $routeRole->hasPermissionTo('manage roles')) {
            return;
        }

        $otherRolesWithManageRoles = Role::whereHas('permissions', function ($query) use ($manageRolesPermission) {
            $query->where('permissions.id', $manageRolesPermission->id);
        })->where('id', '!=', $routeRole->id)->count();

        if ($otherRolesWithManageRoles === 0) {
            $validator->errors()->add(
                'pages',
                'This role is the only one that can manage roles. Grant "manage roles" to another role before changing this one.'
            );
        }
    }

    /**
     * Converts the validated `pages` array into a flat list of Spatie
     * permission names, applying the "Manage implies View" rule
     * server-side — this is the authoritative enforcement of that rule;
     * the client-side auto-check in the form is a convenience, not the
     * source of truth, so a request built by hand (or a future client
     * bug) can't bypass it.
     */
    public function toPermissionNames(): array
    {
        $names = [];

        foreach ($this->input('pages', []) as $slug => $flags) {
            if (! in_array($slug, ManagedPages::slugs(), true)) {
                continue; // already rejected by withValidator, but stay defensive
            }

            $manage = ! empty($flags['manage']);
            $view = ! empty($flags['view']) || $manage; // manage implies view

            if ($view) {
                $names[] = ManagedPages::viewPermission($slug);
            }
            if ($manage) {
                $names[] = ManagedPages::managePermission($slug);
            }
        }

        return $names;
    }
}
