<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tooth_timeline_events', function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('event_type');
            $table->index(['patient_id', 'batch_uuid']);
        });
    }

    public function down(): void
    {
        Schema::table('tooth_timeline_events', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'batch_uuid']);
            $table->dropColumn('batch_uuid');
        });
    }
};
