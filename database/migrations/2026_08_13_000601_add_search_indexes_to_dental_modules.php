<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('name', 'suppliers_name_idx');
        });

        Schema::table('dental_labs', function (Blueprint $table) {
            $table->index('name', 'dental_labs_name_idx');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->index('name', 'assets_name_idx');
            $table->index('category', 'assets_category_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('name', 'users_name_idx');
        });

        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->index('company_name', 'insurance_company_name_idx');
            $table->index('contract_number', 'insurance_contract_number_idx');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_contracts', function (Blueprint $table) {
            $table->dropIndex('insurance_contract_number_idx');
            $table->dropIndex('insurance_company_name_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_name_idx');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex('assets_category_idx');
            $table->dropIndex('assets_name_idx');
        });

        Schema::table('dental_labs', function (Blueprint $table) {
            $table->dropIndex('dental_labs_name_idx');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex('suppliers_name_idx');
        });
    }
};
