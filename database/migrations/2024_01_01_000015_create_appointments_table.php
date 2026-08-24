<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
            // Nullable: only set when this visit is a session of a
            // (usually multi-session) treatment, so sessions can be
            // grouped and counted on the patient/treatment pages.
            $table->foreignId('treatment_id')->nullable()
                ->constrained('treatments')->nullOnDelete();
            $table->unsignedTinyInteger('session_number')->nullable();
            $table->enum('visit_type', ['initial_consultation', 'follow_up']);
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', [
                'scheduled', 'in_progress', 'completed', 'cancelled', 'no_show',
            ])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['room_id', 'appointment_date']);
            $table->index('appointment_date');
            $table->index(['appointment_date', 'start_time', 'end_time', 'status'], 'idx_appointment_conflict');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
