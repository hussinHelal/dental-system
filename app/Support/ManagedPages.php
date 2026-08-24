<?php

namespace App\Support;

/**
 * The fixed list of pages that get View/Manage permission checkboxes on
 * the role form, matching the sidebar in the screenshot (18 pages).
 *
 * Dashboard is deliberately excluded — it stays visible to every
 * authenticated user regardless of role (per explicit decision), so it
 * never gets its own permission pair.
 *
 * Each page gets exactly two permissions, generated from its slug:
 *   view {slug}    — can see the page / read its data
 *   manage {slug}  — can create/edit/delete on the page (implies view)
 *
 * This is the ONE place the page list lives. The role form, the
 * migration that seeds these permissions, and any `@can('view spslug')`
 * check elsewhere in the app should all read from here rather than
 * hardcoding the list a second time — otherwise the sidebar, the
 * permission grid, and the actual gates drift out of sync with each
 * other over time.
 */
class ManagedPages
{
    /**
     * @return array<string, string> slug => translation key suffix
     *   (label is resolved via __('messages.page_'.$slug) in views, so
     *   English/Arabic labels live in the normal translation files
     *   rather than hardcoded here)
     */
    public static function all(): array
    {
        return [
            'appointments' => 'appointments',
            'patients' => 'patients',
            'agenda' => 'agenda',
            'doctors' => 'doctors',
            'rooms' => 'rooms',
            'treatments' => 'treatments',
            'inventory' => 'inventory',
            'revenue-expenses' => 'revenue_expenses',
            'procurement' => 'procurement',
            'purchases' => 'purchases',
            'labs' => 'labs',
            'lab-cases' => 'lab_cases',
            'assets' => 'assets',
            'insurance' => 'insurance',
            'reports' => 'reports',
            'backups' => 'backups',
            'staff' => 'staff',
            'activity-log' => 'activity_log',
        ];
    }

    public static function slugs(): array
    {
        return array_keys(self::all());
    }

    public static function viewPermission(string $slug): string
    {
        return "view {$slug}";
    }

    public static function managePermission(string $slug): string
    {
        return "manage {$slug}";
    }

    /**
     * Every view/manage permission name this app should have, in the
     * order pages are declared. Used by the seed migration so the two
     * lists (this method, and what actually exists in the permissions
     * table) can never silently diverge.
     */
    public static function allPermissionNames(): array
    {
        $names = [];
        foreach (self::slugs() as $slug) {
            $names[] = self::viewPermission($slug);
            $names[] = self::managePermission($slug);
        }

        return $names;
    }
}
