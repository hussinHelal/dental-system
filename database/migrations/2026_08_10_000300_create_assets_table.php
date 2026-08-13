<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 10, 2);
            $table->decimal('salvage_value', 10, 2)->default(0);
            $table->unsignedSmallInteger('useful_life_years')->default(5);
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('purchase_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
