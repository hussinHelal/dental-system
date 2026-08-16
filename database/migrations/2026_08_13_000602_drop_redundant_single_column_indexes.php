<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The composite indexes added in 2026_08_13_000600 make a few of the original
 * single-column indexes redundant — a composite index on (A, B) already serves
 * lookups on A alone (left-prefix rule), so keeping a separate single-column
 * index on A costs write performance for zero additional read benefit.
 *
 * Not redundant, and deliberately left alone: purchases.purchase_date,
 * lab_cases.patient_id, lab_cases.sent_date, insurance_contracts.end_date —
 * each of these only appears as the *second* column in a composite, so a
 * standalone index on it is still needed for queries that filter on it without
 * the composite's first column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_supplier_id_index');
            $table->dropIndex('purchases_payment_status_index');
        });

        Schema::table('lab_cases', function (Blueprint $table) {
            $table->dropIndex('lab_cases_dental_lab_id_index');
            $table->dropIndex('lab_cases_status_index');
        });

        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->dropIndex('insurance_contracts_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('payment_status');
        });

        Schema::table('lab_cases', function (Blueprint $table) {
            $table->index('dental_lab_id');
            $table->index('status');
        });

        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->index('status');
        });
    }
};
