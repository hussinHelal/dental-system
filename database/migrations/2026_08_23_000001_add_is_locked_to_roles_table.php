<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spatie's `roles` table has no "protect this row" concept out of the box.
 * We add is_locked so the seeded Admin Doctor role can never be renamed,
 * stripped of permissions, or deleted from the UI or API — even by another
 * Admin Doctor account. This is a hard backend guard, not just a hidden button.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('guard_name');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
