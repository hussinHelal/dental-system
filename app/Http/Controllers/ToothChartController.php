<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ToothRecord;
use App\Models\ToothTimelineEvent;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Throwable;

class ToothChartController extends Controller
{
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load([
            'toothRecords.treatment',
            'toothTimelineEvents.treatment',
            'toothTimelineEvents.recorder',
        ]);
        
        $treatments = Treatment::active()->orderBy('name')->get();
        
        // Build tooth map: number => record (or null)
        $toothMap = $patient->toothRecords->keyBy('tooth_number');

        $toothHistory = $this->buildToothHistory($patient);

        return view('patients.tooth-chart', compact('patient', 'toothMap', 'treatments', 'toothHistory'));
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
            $batchUuid = Str::uuid()->toString();
            $existingRecord = ToothRecord::where('patient_id', $patient->id)
                ->where('tooth_number', $validated['tooth_number'])
                ->first();

            $existingNotes = trim((string) ($existingRecord?->notes ?? ''));
            $newNotes = trim((string) ($validated['notes'] ?? ''));
            $hasBraces = str_contains($existingNotes, '[braces]');

            $notesParts = [];
            if ($hasBraces) {
                $notesParts[] = '[braces]';
            }
            if ($newNotes !== '') {
                $notesParts[] = $newNotes;
            }

            ToothRecord::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'tooth_number' => $validated['tooth_number'],
                ],
                [
                    'status' => $validated['status'],
                    'treatment_id' => $validated['treatment_id'],
                    'notes' => !empty($notesParts) ? implode("\n", $notesParts) : null,
                    'recorded_by' => $request->user()->id,
                ]
            );

            $this->recordTimelineEvent(
                $patient,
                (int) $validated['tooth_number'],
                'status_applied',
                $validated['status'],
                $validated['treatment_id'] ?? null,
                $validated['notes'] ?? null,
                $request->user()->id,
                $batchUuid
            );
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.tooth_updated'),
                'teeth' => $this->renderTeethPayload($patient, [(int) $validated['tooth_number']]),
                'history_html' => $this->renderToothHistoryHtml($patient),
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
                   $records = [];
                   $batchUuid = Str::uuid()->toString();
                   $isBraces = $validated['status'] === 'braces';
                   $bracesTag = '[braces]';

                   foreach ($validated['tooth_numbers'] as $toothNumber) {
                       $existingRecord = ToothRecord::where('patient_id', $patient->id)
                           ->where('tooth_number', $toothNumber)
                           ->first();

                       if ($isBraces) {
                           $existingNotes = trim((string) ($existingRecord?->notes ?? ''));
                           $newNotes = trim((string) ($validated['notes'] ?? ''));

                           $notesParts = [];
                           if ($existingNotes !== '') {
                               $notesParts[] = $existingNotes;
                           }
                           if (!str_contains($existingNotes, $bracesTag)) {
                               $notesParts[] = $bracesTag;
                           }
                           if ($newNotes !== '') {
                               $notesParts[] = $newNotes;
                           }

                           $records[] = ToothRecord::updateOrCreate(
                               [
                                   'patient_id' => $patient->id,
                                   'tooth_number' => $toothNumber,
                               ],
                               [
                                   'status' => $existingRecord?->status ?? 'healthy',
                                   'treatment_id' => $validated['treatment_id'] ?? $existingRecord?->treatment_id,
                                   'notes' => implode("\n", $notesParts),
                                   'recorded_by' => $request->user()->id,
                               ]
                           );

                           $this->recordTimelineEvent(
                               $patient,
                               $toothNumber,
                               'braces_applied',
                               'braces',
                               $validated['treatment_id'] ?? $existingRecord?->treatment_id,
                               $newNotes !== '' ? $newNotes : null,
                               $request->user()->id,
                               $batchUuid
                           );

                           continue;
                       }

                       $existingNotes = trim((string) ($existingRecord?->notes ?? ''));
                       $newNotes = trim((string) ($validated['notes'] ?? ''));
                       $hasBraces = str_contains($existingNotes, '[braces]');

                       $notesParts = [];
                       if ($hasBraces) {
                           $notesParts[] = '[braces]';
                       }
                       if ($newNotes !== '') {
                           $notesParts[] = $newNotes;
                       }

                       $records[] = ToothRecord::updateOrCreate(
                           [
                               'patient_id' => $patient->id,
                               'tooth_number' => $toothNumber,
                           ],
                           [
                               'status' => $validated['status'],
                               'treatment_id' => $validated['treatment_id'] ?? null,
                               'notes' => !empty($notesParts) ? implode("\n", $notesParts) : null,
                               'recorded_by' => $request->user()->id,
                           ]
                       );

                       $this->recordTimelineEvent(
                           $patient,
                           $toothNumber,
                           'status_applied',
                           $validated['status'],
                           $validated['treatment_id'] ?? null,
                           $validated['notes'] ?? null,
                           $request->user()->id,
                           $batchUuid
                       );
                   }
   
                   return $records;
               });
   
               return response()->json([
                   'success' => true,
                   'message' => $validated['status'] === 'braces'
                       ? __('messages.braces_added_without_replacing_status')
                       : __('messages.tooth_updated'),
                   'updated_count' => count($updatedRecords),
                   'tooth_numbers' => $validated['tooth_numbers'],
                   'status' => $validated['status'],
                   'preserved_existing_status' => $validated['status'] === 'braces',
                   'teeth' => $this->renderTeethPayload($patient, $validated['tooth_numbers']),
                   'history_html' => $this->renderToothHistoryHtml($patient),
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
               $deletedCount = DB::transaction(function () use ($validated, $patient, $request) {
                   $batchUuid = Str::uuid()->toString();
                   $records = ToothRecord::where('patient_id', $patient->id)
                       ->whereIn('tooth_number', $validated['tooth_numbers'])
                       ->get();

                   foreach ($records as $record) {
                       $this->recordTimelineEvent(
                           $patient,
                           $record->tooth_number,
                           'status_applied',
                           'healthy',
                           null,
                           null,
                           $request->user()->id,
                           $batchUuid
                       );
                   }

                   return ToothRecord::where('patient_id', $patient->id)
                       ->whereIn('tooth_number', $validated['tooth_numbers'])
                       ->delete();
               });
   
               return response()->json([
                   'success' => true,
                   'message' => __('messages.tooth_reset'),
                   'deleted_count' => $deletedCount,
                   'tooth_numbers' => $validated['tooth_numbers'],
                   'teeth' => $this->renderTeethPayload($patient, $validated['tooth_numbers']),
                   'history_html' => $this->renderToothHistoryHtml($patient),
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


    public function bulkRemoveBraces(Request $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'tooth_numbers' => ['required', 'array', 'min:1', 'max:32'],
            'tooth_numbers.*' => ['integer', 'distinct', 'between:1,32'],
        ]);

        try {
            $updatedCount = DB::transaction(function () use ($validated, $patient, $request) {
                $count = 0;
                $batchUuid = Str::uuid()->toString();

                $records = ToothRecord::where('patient_id', $patient->id)
                    ->whereIn('tooth_number', $validated['tooth_numbers'])
                    ->get();

                foreach ($records as $record) {
                    $originalNotes = trim((string) ($record->notes ?? ''));
                    $cleanedNotes = preg_replace('/(^|\R)\[braces\](\R|$)/', '$1', $originalNotes ?? '');
                    $cleanedNotes = preg_replace('/\R{2,}/', "\n", (string) $cleanedNotes);
                    $cleanedNotes = trim((string) $cleanedNotes);

                    if ($record->status === 'braces') {
                        $this->recordTimelineEvent(
                            $patient,
                            $record->tooth_number,
                            'braces_removed',
                            'braces',
                            $record->treatment_id,
                            null,
                            $request->user()->id,
                            $batchUuid
                        );

                        $record->delete();
                        $count++;
                        continue;
                    }

                    if ($cleanedNotes !== $originalNotes) {
                        $record->notes = $cleanedNotes !== '' ? $cleanedNotes : null;
                        $record->save();
                        $this->recordTimelineEvent(
                            $patient,
                            $record->tooth_number,
                            'braces_removed',
                            'braces',
                            $record->treatment_id,
                            null,
                            $request->user()->id,
                            $batchUuid
                        );
                        $count++;
                    }
                }

                return $count;
            });

            return response()->json([
                'success' => true,
                'message' => __('messages.bulk_braces_cleared'),
                'updated_count' => $updatedCount,
                'tooth_numbers' => $validated['tooth_numbers'],
                'teeth' => $this->renderTeethPayload($patient, $validated['tooth_numbers']),
                'history_html' => $this->renderToothHistoryHtml($patient),
            ]);
        } catch (Throwable $e) {
            Log::error('Bulk braces removal failed', [
                'patient_id' => $patient->id,
                'tooth_numbers' => $validated['tooth_numbers'] ?? [],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not remove braces from the selected teeth. No changes were saved.',
            ], 500);
        }
    }

    private function recordTimelineEvent(
        Patient $patient,
        int $toothNumber,
        string $eventType,
        ?string $status,
        ?int $treatmentId,
        ?string $notes,
        ?int $recordedBy,
        ?string $batchUuid = null,
    ): void {
        ToothTimelineEvent::create([
            'patient_id' => $patient->id,
            'tooth_number' => $toothNumber,
            'event_type' => $eventType,
            'status' => $status,
            'treatment_id' => $treatmentId,
            'notes' => $notes,
            'recorded_by' => $recordedBy,
            'batch_uuid' => $batchUuid,
        ]);
    }

    private function buildToothHistory(Patient $patient)
    {
        $patient->loadMissing([
            'toothTimelineEvents.treatment',
            'toothTimelineEvents.recorder',
        ]);

        return $patient->toothTimelineEvents
            ->sortByDesc('created_at')
            ->groupBy(function (ToothTimelineEvent $event) {
                if ($event->batch_uuid) {
                    return 'batch:'.$event->batch_uuid;
                }

                return implode('|', [
                    'legacy',
                    $event->event_type,
                    $event->status,
                    $event->treatment_id,
                    trim((string) $event->notes),
                    $event->recorded_by,
                    optional($event->created_at)->format('Y-m-d H:i'),
                ]);
            })
            ->map(function ($events) {
                $first = $events->first();

                return [
                    'batch_uuid' => $first?->batch_uuid,
                    'event_type' => $first?->event_type,
                    'status' => $first?->status,
                    'treatment_name' => $first?->treatment?->name,
                    'notes' => $first?->notes,
                    'created_at' => $first?->created_at,
                    'recorder_name' => $first?->recorder?->name,
                    'tooth_numbers' => $events->pluck('tooth_number')->map(fn ($n) => (int) $n)->unique()->sort()->values(),
                ];
            })
            ->sortByDesc('created_at')
            ->values();
    }

    private function renderToothHistoryHtml(Patient $patient): string
    {
        return View::make('patients.partials.tooth-history-list', [
            'toothHistory' => $this->buildToothHistory($patient),
        ])->render();
    }

    private function renderTeethPayload(Patient $patient, array $toothNumbers): array
    {
        $records = ToothRecord::where('patient_id', $patient->id)
            ->whereIn('tooth_number', $toothNumbers)
            ->get()
            ->keyBy('tooth_number');

        $payload = [];

        foreach ($toothNumbers as $toothNumber) {
            $record = $records->get($toothNumber);
            $payload[] = [
                'tooth_number' => $toothNumber,
                'status' => $record?->status ?? 'healthy',
                'treatment_id' => $record?->treatment_id,
                'notes' => $record?->notes,
                'html' => View::make('patients.partials.tooth', [
                    'number' => $toothNumber,
                    'record' => $record,
                    'label' => $this->displayLabelForTooth($toothNumber),
                ])->render(),
            ];
        }

        return $payload;
    }

    private function displayLabelForTooth(int $toothNumber): int
    {
        return match (true) {
            $toothNumber >= 1 && $toothNumber <= 8 => $toothNumber,
            $toothNumber >= 9 && $toothNumber <= 16 => 17 - $toothNumber,
            $toothNumber >= 17 && $toothNumber <= 24 => 25 - $toothNumber,
            $toothNumber >= 25 && $toothNumber <= 32 => $toothNumber - 24,
            default => $toothNumber,
        };
    }
}
