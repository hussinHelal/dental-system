<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Recreate the tooth_records table with the corrected CHECK constraint (include 'veneer')
        DB::transaction(function () {
            // Create new table with correct constraint
            DB::statement(<<<'SQL'
CREATE TABLE tooth_records_new (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER NOT NULL,
    tooth_number INTEGER NOT NULL,
    status TEXT NOT NULL DEFAULT 'healthy' CHECK (status IN (
        'healthy','decayed','filled','crown','root_canal',
        'extracted','missing','implant','fractured','abscess',
        'wisdom','braces','veneer'
    )),
    notes TEXT,
    treatment_id INTEGER,
    recorded_by INTEGER,
    created_at DATETIME,
    updated_at DATETIME,
    UNIQUE(patient_id, tooth_number)
    );
SQL
            );

            if (Schema::hasTable('tooth_records')) {
                // Copy data from old table (rows with status values not matching the new CHECK will cause this to fail)
                DB::statement('INSERT INTO tooth_records_new (id, patient_id, tooth_number, status, notes, treatment_id, recorded_by, created_at, updated_at) SELECT id, patient_id, tooth_number, status, notes, treatment_id, recorded_by, created_at, updated_at FROM tooth_records');

                // Drop old table
                DB::statement('DROP TABLE tooth_records');
            }

            // Rename new table into place
            DB::statement('ALTER TABLE tooth_records_new RENAME TO tooth_records');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op reverse: the original migration file has been corrected — if rollback is required, recreate original table manually.
    }
};
