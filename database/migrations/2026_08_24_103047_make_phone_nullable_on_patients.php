<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->change();
            $table->text('address')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Reversing this would fail if any nullable phones exist by
        // then — deliberately not auto-filling a fake value to satisfy
        // a NOT NULL rollback. If you need to roll back, backfill or
        // delete null-phone rows first.
        Schema::table('patients', function (Blueprint $table) {
            $table->string('phone', 30)->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
        });
    }
};