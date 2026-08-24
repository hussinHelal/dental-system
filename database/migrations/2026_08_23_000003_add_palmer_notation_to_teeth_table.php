<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->validateExistingToothNumbers();

        Schema::table('tooth_chart_entries', function (Blueprint $table) {
            $table->string('palmer_quadrant', 2)->nullable()->after('tooth_number');
            $table->unsignedTinyInteger('palmer_position')->nullable()->after('palmer_quadrant');
        });

        $this->backfillPalmerValues();

        Schema::table('tooth_chart_entries', function (Blueprint $table) {
            $table->string('palmer_quadrant', 2)->nullable(false)->change();
            $table->unsignedTinyInteger('palmer_position')->nullable(false)->change();
            $table->index(['palmer_quadrant', 'palmer_position']);
        });
    }

    public function down(): void
    {
        Schema::table('tooth_chart_entries', function (Blueprint $table) {
            $table->dropIndex(['palmer_quadrant', 'palmer_position']);
            $table->dropColumn(['palmer_quadrant', 'palmer_position']);
        });
    }

    private function validateExistingToothNumbers(): void
    {
        DB::table('tooth_chart_entries')
            ->select('id', 'tooth_number')
            ->orderBy('id')
            ->chunkById(500, function ($teeth): void {
                foreach ($teeth as $tooth) {
                    $number = (int) $tooth->tooth_number;
                    if ($number < 1 || $number > 32 || (string) $number !== (string) $tooth->tooth_number) {
                        throw new \RuntimeException(sprintf(
                            'Cannot add Palmer notation: tooth id %s has invalid tooth_number %s. Fix the row and rerun the migration.',
                            $tooth->id,
                            var_export($tooth->tooth_number, true)
                        ));
                    }
                }
            });
    }

    private function backfillPalmerValues(): void
    {
        DB::table('tooth_chart_entries')
            ->select('id', 'tooth_number')
            ->orderBy('id')
            ->chunkById(500, function ($teeth): void {
                foreach ($teeth as $tooth) {
                    [$quadrant, $position] = $this->universalToPalmer((int) $tooth->tooth_number);

                    if ($position === 0) {
                       
                        throw new \RuntimeException(sprintf(
                            'Cannot backfill Palmer notation for tooth id %s: invalid tooth_number %s. Fix the row and rerun the migration.',
                            $tooth->id,
                            var_export($tooth->tooth_number, true)
                        ));
                    }

                    DB::table('tooth_chart_entries')
                        ->where('id', $tooth->id)
                        ->update([
                            'palmer_quadrant' => $quadrant,
                            'palmer_position' => $position,
                        ]);
                }
            });
    }

    /**
     * @return array{0: string, 1: int} [quadrant, position]
     */
    private function universalToPalmer(int $universalNumber): array
    {
        return match (true) {
            $universalNumber >= 1 && $universalNumber <= 8 => ['UR', 9 - $universalNumber],
            $universalNumber >= 9 && $universalNumber <= 16 => ['UL', $universalNumber - 8],
            $universalNumber >= 17 && $universalNumber <= 24 => ['LL', 25 - $universalNumber],
            $universalNumber >= 25 && $universalNumber <= 32 => ['LR', $universalNumber - 24],
            
            default => ['UR', 0],
        };
    }
};