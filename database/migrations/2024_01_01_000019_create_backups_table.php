<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path');
            $table->enum('type', ['pdf', 'excel', 'both', 'database']);
            $table->enum('status', ['queued', 'completed', 'failed'])
                ->default('completed');
            $table->unsignedBigInteger('size_bytes')->default(0);
            // Null generated_by means it was produced by the scheduled
            // monthly job rather than a manual "Backup Now" click.
            $table->foreignId('generated_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
