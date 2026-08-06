<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tooth_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('tooth_number'); // 1-32 (Universal) or 11-48 (FDI)
            $table->enum('status', [
                'healthy', 'decayed', 'filled', 'crown', 'root_canal',
                'extracted', 'missing', 'implant', 'fractured', 'abscess',
                'wisdom', 'braces', 'veneer'
            ])->default('healthy');
            $table->text('notes')->nullable();
            $table->foreignId('treatment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['patient_id', 'tooth_number']);
            $table->index(['patient_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tooth_records');
    }
};
