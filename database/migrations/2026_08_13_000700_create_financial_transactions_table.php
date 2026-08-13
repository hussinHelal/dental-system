<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date')->index();
            $table->string('type', 20)->index(); // income | expense
            $table->string('category', 100)->nullable()->index();
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'transaction_date'], 'financial_type_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
