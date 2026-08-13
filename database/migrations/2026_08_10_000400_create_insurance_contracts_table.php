<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('contract_number')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('coverage_details')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('status')->default('active'); // active | expired | cancelled
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_contracts');
    }
};
