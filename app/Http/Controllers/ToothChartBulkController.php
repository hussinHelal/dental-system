<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkToothTreatmentRequest;
use App\Models\Tooth;
use App\Models\ToothTreatment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

/**
 * Bulk tooth-treatment operations for the odontogram.
 *
 * Design note (per explicit requirement): the checkbox list lets a user
 * select any subset of teeth — not forced to all 32 — and a "select all"
 * checkbox is just a UI convenience that fills the same array. Either way,
 * exactly ONE request reaches the backend, applying/removing one treatment
 * record per selected tooth inside a single DB transaction. If any tooth
 * fails, the whole batch rolls back — a bulk action must not leave the
 * chart in a half-applied state.
 *
 * NOTE: these two methods (bulkApply / bulkRemove) are written as their own
 * controller for delivery purposes. Merge them into the existing
 * ToothChartController in the host app rather than routing to a second
 * controller for the same resource.
 */
class ToothChartBulkController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage tooth chart']);
    }

    public function bulkApply(BulkToothTreatmentRequest $request, int $patientId): JsonResponse
    {
        $validated = $request->validated();

        try {
            $created = DB::transaction(function () use ($validated, $patientId) {
                // Lock the target teeth rows for the duration of the
                // transaction so two staff members bulk-applying at the
                // same moment can't both pass the ownership check and
                // then race past each other into the insert below.
                $teeth = Tooth::where('patient_id', $patientId)
                    ->whereIn('id', $validated['tooth_ids'])
                    ->lockForUpdate()
                    ->get();

                if ($teeth->count() !== count($validated['tooth_ids'])) {
                    // Defensive: exists:teeth,id in the FormRequest doesn't
                    // guarantee the tooth belongs to THIS patient. Without
                    // this check a crafted request could apply a treatment
                    // to another patient's tooth by id. We throw a plain
                    // exception (not abort()) because abort()'s
                    // HttpException does NOT carry its status via
                    // getCode() — Symfony always sets the exception code
                    // to 0, so catching on getCode()===422 would silently
                    // never match. A dedicated exception type is unambiguous.
                    throw new InvalidArgumentException('One or more selected teeth do not belong to this patient.');
                }

                // lockForUpdate() alone does NOT stop duplicate rows here —
                // it only serializes access to rows that already exist; a
                // plain insert() has no uniqueness check of its own. An
                // earlier version of this method inserted unconditionally,
                // so clicking "Apply: Veneer" twice on the same tooth (or
                // two staff doing it near-simultaneously) would silently
                // create two identical treatment rows for one tooth.
                // Re-applying the same treatment to an already-treated
                // tooth should be a no-op, not a duplicate, so we skip
                // teeth that already have this exact treatment_type.
                $alreadyTreatedToothIds = ToothTreatment::whereIn('tooth_id', $teeth->pluck('id'))
                    ->where('treatment_type', $validated['treatment_type'])
                    ->pluck('tooth_id')
                    ->all();

                $teethToInsert = $teeth->reject(
                    fn (Tooth $tooth) => in_array($tooth->id, $alreadyTreatedToothIds, true)
                );

                if ($teethToInsert->isEmpty()) {
                    return 0;
                }

                $now = now();
                $records = $teethToInsert->map(fn (Tooth $tooth) => [
                    'tooth_id' => $tooth->id,
                    'treatment_type' => $validated['treatment_type'],
                    'note' => $validated['note'] ?? null,
                    'applied_by' => auth()->id(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                ToothTreatment::insert($records);

                return $teethToInsert->count();
            });

            $skippedCount = count($validated['tooth_ids']) - $created;
            $message = $created > 0
                ? "Applied \"{$validated['treatment_type']}\" to {$created} tooth/teeth."
                    .($skippedCount > 0 ? " {$skippedCount} already had this treatment and were left unchanged." : '')
                : "All selected teeth already have \"{$validated['treatment_type']}\" applied — nothing to do.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'applied_count' => $created,
                'skipped_count' => $skippedCount,
                'tooth_ids' => $validated['tooth_ids'],
                'treatment_type' => $validated['treatment_type'],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Bulk tooth treatment apply failed', [
                'patient_id' => $patientId,
                'tooth_ids' => $validated['tooth_ids'] ?? [],
                'treatment_type' => $validated['treatment_type'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not apply the treatment. No changes were saved.',
            ], 500);
        }
    }

    public function bulkRemove(BulkToothTreatmentRequest $request, int $patientId): JsonResponse
    {
        $validated = $request->validated();

        try {
            $removed = DB::transaction(function () use ($validated, $patientId) {
                $teethIds = Tooth::where('patient_id', $patientId)
                    ->whereIn('id', $validated['tooth_ids'])
                    ->lockForUpdate()
                    ->pluck('id');

                if ($teethIds->count() !== count($validated['tooth_ids'])) {
                    throw new InvalidArgumentException('One or more selected teeth do not belong to this patient.');
                }

                // Report distinct teeth affected, not raw deleted-row
                // count. If a tooth ever ended up with more than one
                // ToothTreatment row for the same treatment_type (e.g.
                // a duplicate from before this bulk endpoint existed),
                // ->delete()'s return value would be a row count that
                // could exceed the number of teeth selected — which
                // would make the "Removed from N tooth/teeth" message
                // to the user actively wrong, not just imprecise.
                $affectedTeethCount = ToothTreatment::whereIn('tooth_id', $teethIds)
                    ->where('treatment_type', $validated['treatment_type'])
                    ->distinct()
                    ->count('tooth_id');

                ToothTreatment::whereIn('tooth_id', $teethIds)
                    ->where('treatment_type', $validated['treatment_type'])
                    ->delete();

                return $affectedTeethCount;
            });

            return response()->json([
                'success' => true,
                'message' => "Removed \"{$validated['treatment_type']}\" from {$removed} tooth/teeth.",
                'removed_count' => $removed,
                'tooth_ids' => $validated['tooth_ids'],
                'treatment_type' => $validated['treatment_type'],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Bulk tooth treatment remove failed', [
                'patient_id' => $patientId,
                'tooth_ids' => $validated['tooth_ids'] ?? [],
                'treatment_type' => $validated['treatment_type'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not remove the treatment. No changes were saved.',
            ], 500);
        }
    }
}
