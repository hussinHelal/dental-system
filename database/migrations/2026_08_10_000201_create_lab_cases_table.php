<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dental_lab_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('case_type'); // e.g. crown, denture, bridge
            $table->date('sent_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('status')->default('sent'); // sent | in_progress | received | delivered
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('dental_lab_id');
            $table->index('patient_id');
            $table->index('status');
            $table->index('sent_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_cases');
    }
};
