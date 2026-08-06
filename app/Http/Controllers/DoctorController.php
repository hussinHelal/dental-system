<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\DoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    use HandlesImageUploads, RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Doctor::class);

        $doctors = Doctor::search($request->query('q'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('doctors.index', compact('doctors'));
    }

    public function store(DoctorRequest $request)
    {
        $this->authorize('create', Doctor::class);

        $data = $request->validated();
        $data['working_hours'] = array_filter($data['working_hours'] ?? [], fn ($value) => filled($value)) ?: null;

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'doctors');
        }

        Doctor::create($data);

        return $this->respondSuccess($request, __('messages.doctor_created'), 'doctors.index');
    }

    public function update(DoctorRequest $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $data = $request->validated();
        $data['working_hours'] = array_filter($data['working_hours'] ?? [], fn ($value) => filled($value)) ?: null;

        if ($request->hasFile('photo')) {
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'doctors');
        }

        $doctor->update($data);

        return $this->respondSuccess($request, __('messages.doctor_updated'), 'doctors.index');
    }

    public function destroy(Request $request, Doctor $doctor)
    {
        $this->authorize('delete', $doctor);

        // Never hard-delete: deactivate so historical appointments and
        // payment records tied to this doctor stay intact.
        $doctor->update(['is_active' => false]);

        return $this->respondSuccess($request, __('messages.doctor_deactivated'), 'doctors.index');
    }

    public function reactivate(Request $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $doctor->update(['is_active' => true]);

        return $this->respondSuccess($request, __('messages.doctor_reactivated'), 'doctors.index');
    }
}
