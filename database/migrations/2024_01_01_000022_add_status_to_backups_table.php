<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            // 'completed' as the default keeps every pre-existing row
            // (created the old, synchronous way) valid without a
            // separate data migration.
            $table->enum('status', ['queued', 'completed', 'failed'])
                ->default('completed')
                ->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
