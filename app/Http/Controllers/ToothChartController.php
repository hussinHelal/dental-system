<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ToothRecord;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use PHPUnit\Event\Code\Throwable;

class ToothChartController extends Controller
{
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load('toothRecords.treatment');
        
        $treatments = Treatment::active()->orderBy('name')->get();
        
        // Build tooth map: number => record (or null)
        $toothMap = $patient->toothRecords->keyBy('tooth_number');

        return view('patients.tooth-chart', compact('patient', 'toothMap', 'treatments'));
    }

    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'tooth_number' => ['required', 'integer', 'between:1,32'],
            'status' => ['required', Rule::in([
                'healthy', 'decayed', 'filled', 'crown', 'root_canal',
                'extracted', 'missing', 'implant', 'fractured', 'abscess',
                'wisdom', 'braces', 'veneer'
            ])],
            'treatment_id' => ['nullable', 'exists:treatments,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($patient, $validated, $request) {
            ToothRecord::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'tooth_number' => $validated['tooth_number'],
                ],
                [
                    'status' => $validated['status'],
                    'treatment_id' => $validated['treatment_id'],
                    'notes' => $validated['notes'],
                    'recorded_by' => $request->user()->id,
                ]
            );
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.tooth_updated'),
            ]);
        }

        return back()->with('success', __('messages.tooth_updated'));
    }

    public function destroy(Request $request, Patient $patient, int $toothNumber)
    {
        $this->authorize('update', $patient);

        ToothRecord::where('patient_id', $patient->id)
            ->where('tooth_number', $toothNumber)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', __('messages.tooth_reset'));
    }

    public function bulkApply(Request $request, Patient $patient): JsonResponse
       {
           $this->authorize('update', $patient);
   
           $validated = $request->validate([
               'tooth_numbers' => ['required', 'array', 'min:1', 'max:32'],
               'tooth_numbers.*' => ['integer', 'distinct', 'between:1,32'],
               'status' => ['required', Rule::in([
                   'healthy', 'decayed', 'filled', 'crown', 'root_canal',
                   'extracted', 'missing', 'implant', 'fractured', 'abscess',
                   'wisdom', 'braces', 'veneer',
               ])],
               'treatment_id' => ['nullable', 'exists:treatments,id'],
               'notes' => ['nullable', 'string', 'max:500'],
           ]);
   
           try {
               $updatedRecords = DB::transaction(function () use ($validated, $patient, $request) {
                   $now = now();
   
                   // updateOrCreate per tooth_number, same as the existing
                   // single-tooth update() method — this keeps bulk apply and
                   // single-tooth edit producing identical row shapes, so
                   // nothing downstream (reports, the chart's own status
                   // colors) needs to know which path created a record.
                   //
                   // One query per tooth here (not a single bulk insert) is
                   // intentional: updateOrCreate has to individually decide
                   // insert-vs-update per tooth_number, since some selected
                   // teeth may already have a ToothRecord row and others may
                   // not. This still costs the caller exactly ONE HTTP
                   // request either way, which is what was asked for — the
                   // per-tooth queries happen server-side inside a single
                   // transaction, not as separate round trips from the
                   // browser.
                   $records = [];
                   foreach ($validated['tooth_numbers'] as $toothNumber) {
                       $records[] = ToothRecord::updateOrCreate(
                           [
                               'patient_id' => $patient->id,
                               'tooth_number' => $toothNumber,
                           ],
                           [
                               'status' => $validated['status'],
                               'treatment_id' => $validated['treatment_id'] ?? null,
                               'notes' => $validated['notes'] ?? null,
                               'recorded_by' => $request->user()->id,
                           ]
                       );
                   }
   
                   return $records;
               });
   
               return response()->json([
                   'success' => true,
                   'message' => __('messages.tooth_updated'),
                   'updated_count' => count($updatedRecords),
                   'tooth_numbers' => $validated['tooth_numbers'],
                   'status' => $validated['status'],
               ]);
           } catch (Throwable $e) {
               Log::error('Bulk tooth chart apply failed', [
                   'patient_id' => $patient->id,
                   'tooth_numbers' => $validated['tooth_numbers'] ?? [],
                   'status' => $validated['status'] ?? null,
                   'error' => $e->getMessage(),
               ]);
   
               return response()->json([
                   'success' => false,
                   'message' => 'Could not update the selected teeth. No changes were saved.',
               ], 500);
           }
       }
   
       public function bulkReset(Request $request, Patient $patient): JsonResponse
       {
           $this->authorize('update', $patient);
   
           $validated = $request->validate([
               'tooth_numbers' => ['required', 'array', 'min:1', 'max:32'],
               'tooth_numbers.*' => ['integer', 'distinct', 'between:1,32'],
           ]);
   
           try {
               $deletedCount = DB::transaction(function () use ($validated, $patient) {
                   // Matches the existing single-tooth destroy() method:
                   // a "reset" is a hard delete of the ToothRecord row, which
                   // your chart already treats as falling back to 'healthy'
                   // (see $toothMap[$num] ?? null in the Blade view).
                   return ToothRecord::where('patient_id', $patient->id)
                       ->whereIn('tooth_number', $validated['tooth_numbers'])
                       ->delete();
               });
   
               return response()->json([
                   'success' => true,
                   'message' => __('messages.tooth_reset'),
                   'deleted_count' => $deletedCount,
                   'tooth_numbers' => $validated['tooth_numbers'],
               ]);
           } catch (Throwable $e) {
               Log::error('Bulk tooth chart reset failed', [
                   'patient_id' => $patient->id,
                   'tooth_numbers' => $validated['tooth_numbers'] ?? [],
                   'error' => $e->getMessage(),
               ]);
   
               return response()->json([
                   'success' => false,
                   'message' => 'Could not reset the selected teeth. No changes were saved.',
               ], 500);
           }
       }
}