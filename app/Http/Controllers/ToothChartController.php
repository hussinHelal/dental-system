<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ToothRecord;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
}