<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tooth_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number');
            $table->string('event_type', 50);
            $table->string('status', 50)->nullable();
            $table->foreignId('treatment_id')->nullable()->constrained('treatments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['patient_id', 'tooth_number', 'created_at']);
            $table->index(['patient_id', 'event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tooth_timeline_events');
    }
};
