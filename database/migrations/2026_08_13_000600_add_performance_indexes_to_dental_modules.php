<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->index(['supplier_id', 'purchase_date'], 'purchases_supplier_date_idx');
            $table->index(['payment_status', 'purchase_date'], 'purchases_status_date_idx');
        });

        Schema::table('lab_cases', function (Blueprint $table) {
            $table->index(['dental_lab_id', 'sent_date'], 'lab_cases_lab_sent_idx');
            $table->index(['status', 'sent_date'], 'lab_cases_status_sent_idx');
        });

        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->index(['status', 'end_date'], 'insurance_status_end_idx');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->dropIndex('insurance_status_end_idx');
        });

        Schema::table('lab_cases', function (Blueprint $table) {
            $table->dropIndex('lab_cases_lab_sent_idx');
            $table->dropIndex('lab_cases_status_sent_idx');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_supplier_date_idx');
            $table->dropIndex('purchases_status_date_idx');
        });
    }
};
