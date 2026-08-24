<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('quantity')->default(0);
            $table->enum('unit', ['box', 'piece', 'ml']);
            $table->string('category')->nullable();
            $table->string('photo')->nullable();
            $table->unsignedInteger('low_stock_threshold')->default(0);
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
