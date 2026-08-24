<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone', 30)->nullable()->index();
            $table->date('date_of_birth')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('notes')->nullable();
            $table->string('photo')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('xray_photo')->nullable()->after('photo');
            $table->json('tooth_chart')->nullable()->after('xray_photo');
            $table->string('crown_color', 50)->nullable()->after('tooth_chart');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
