<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    use HandlesImageUploads, RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::search($request->query('q'))
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);

        $patient->load([
            'appointments.doctor',
            'appointments.room',
            'appointments.treatment',
            'payments.treatment',
            'payments.installments',
        ]);

        $summary = $patient->paymentSummary();

        return view('patients.show', compact('patient', 'summary'));
    }

    public function store(PatientRequest $request)
    {
        $this->authorize('create', Patient::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'patients');
        }

        $patient = Patient::create($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.patient_created'),
                'patient' => $patient,
                'redirect' => route('patients.show', $patient),
            ]);
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', __('messages.patient_created'));
    }

    public function update(PatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($patient->photo) {
                Storage::disk('public')->delete($patient->photo);
            }
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'patients');
        }

        $patient->update($data);

        return $this->respondSuccess($request, __('messages.patient_updated'), 'patients.show', ['patient' => $patient]);
    }

    public function destroy(Request $request, Patient $patient)
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return $this->respondSuccess($request, __('messages.patient_deleted'), 'patients.index');
    }
}
