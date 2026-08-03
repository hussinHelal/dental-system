<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('username');
            // Persisted per-user (not localStorage) so it follows the
            // user across devices, per the resolved theme assumption.
            $table->string('theme')->default('light')->after('avatar');
            // Soft-disable for receptionist accounts; the primary Doctor
            // account is protected from deletion/deactivation in code.
            $table->boolean('is_active')->default(true)->after('theme');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'theme', 'is_active']);
        });
    }
};
