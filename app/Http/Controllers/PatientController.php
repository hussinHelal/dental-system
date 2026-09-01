<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use App\Models\PatientPictureHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    use HandlesImageUploads, RespondsToModals;


    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $query = Patient::query();

        if ($request->filled('q')) {
            $q = trim($request->query('q'));

            $query->where(function ($subQuery) use ($q) {
                if (is_numeric($q)) {
                    // Strictly match Patient ID or exact Phone number
                    $subQuery->where('id', $q)
                            ->orWhere('phone', $q);
                } else {
                    // If searching text, match full_name or partial phone
                    $subQuery->where('full_name', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%");
                }
            });
        }

        $patients = $query->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $appointments = $patient->appointments()
            ->with(['doctor', 'room', 'treatment'])
            ->paginate(10);

        $payments = $patient->payments()
            ->with(['treatment', 'installments' => fn ($query) => $query->orderBy('paid_date')])
            ->latest()
            ->paginate(10);

        $summary = $patient->paymentSummary();

        return view('patients.show', compact(
            'patient', 'appointments', 'payments', 'summary'
        ));
    }

    public function store(PatientRequest $request)
    {
        $this->authorize('create', Patient::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'patients');
        }

        if ($request->hasFile('xray_photo')) {
            $data['xray_photo'] = $this->storeResizedImage(
                $request->file('xray_photo'), 'patients/xrays', 1600
            );
        }

        $data['tooth_chart'] = $this->defaultToothChart();
        

        $patient = Patient::create($data);

        return $this->respondSuccess(
            $request,
            __('messages.patient_created'),
            'patients.show',
            ['patient' => $patient]
        );
    }

    public function update(PatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validated();

        if ($request->has('remove_photo') && $patient->photo) {
            Storage::disk('public')->delete($patient->photo);
            $data['photo'] = null;
        }
        if ($request->has('remove_xray') && $patient->xray_photo) {
            Storage::disk('public')->delete($patient->xray_photo);
            $data['xray_photo'] = null;
        }
     
        if ($request->hasFile('photo')) {
            if ($patient->photo) {
                Storage::disk('public')->delete($patient->photo);
            }
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'patients');
        }

        if ($request->hasFile('xray_photo')) {
            if ($patient->xray_photo) {
                Storage::disk('public')->delete($patient->xray_photo);
            }
            $data['xray_photo'] = $this->storeResizedImage(
                $request->file('xray_photo'), 'patients/xrays', 1600
            );
        }

        if ($request->hasFile('patient_card_picture')) {
            $cardFile = $request->file('patient_card_picture');
            $cardPath = $cardFile->store("patients/{$patient->id}/pictures", 'public');

            PatientPictureHistory::create([
                'patient_id' => $patient->id,
                'picture_type' => 'patient_card',
                'picture_path' => $cardPath,
                'notes' => null,
                'uploaded_by' => $request->user()?->id,
            ]);
        }

        if ($request->has('tooth_chart')) {
            $allowed = ['healthy', 'decayed', 'treated', 'missing', 'root_canal', 'crown'];
            $cleaned = [];
            foreach ($request->input('tooth_chart', []) as $num => $status) {
                if (is_numeric($num) && in_array($status, $allowed, true)) {
                    $cleaned[$num] = $status;
                }
            }
            $data['tooth_chart'] = $cleaned ?: $this->defaultToothChart();
        }

        $patient->update($data);

        return $this->respondSuccess(
            $request,
            __('messages.patient_updated'),
            'patients.show',
            ['patient' => $patient]
        );
    }

    public function destroy(Request $request, Patient $patient)
    {
        $this->authorize('delete', $patient);

        if ($patient->photo) {
            Storage::disk('public')->delete($patient->photo);
        }
        if ($patient->xray_photo) {
            Storage::disk('public')->delete($patient->xray_photo);
        }
       
        $patient->delete();

        return $this->respondSuccess(
            $request,
            __('messages.patient_deleted'),
            'patients.index'
        );
    }

    private function defaultToothChart(): array
    {
        $chart = [];
        for ($i = 1; $i <= 32; $i++) {
            $chart[(string) $i] = 'healthy';
        }
        return $chart;
    }

    //     }

    //     return Patient::search($q)
    //         ->orderBy('full_name')
    //         ->limit(10)
    //         ->get(['id', 'full_name', 'phone']);
    // }
   public function search(Request $request)
{
    $q = trim($request->query('q', ''));

    if ($q === '') {
        return response()->json([]);
    }

    return Patient::query()
        ->where(function ($query) use ($q) {
            if (is_numeric($q)) {
                // If numeric, match ID directly OR exact phone number
                $query->where('id', $q)
                      ->orWhere('phone', $q);
            } else {
                // If text, match name or partial phone
                $query->where('full_name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            }
        })
        ->orderBy('full_name')
        ->limit(10)
        ->get(['id', 'full_name', 'phone']);
}
}