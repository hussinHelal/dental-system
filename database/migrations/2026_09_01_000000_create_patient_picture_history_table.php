<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_picture_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            // Picture types: xray and patient_card only (crown_color is text-based, not a picture)
            $table->enum('picture_type', ['xray', 'patient_card'])->default('xray');
            $table->string('picture_path');
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            // Composite index for efficient queries by patient and type
            $table->index(['patient_id', 'picture_type']);
            $table->index('created_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_picture_history');
    }
};
