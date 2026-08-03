<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Room;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    use RespondsToModals;

    /**
     * Day-view timeline: opening to closing hours for one date,
     * filterable by doctor/room, with prev/next/jump-to-date nav.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : Carbon::today()->toDateString();

        $bookFor = $request->query('book_for');

        $appointments = Appointment::forDate($date)
            ->when($request->query('doctor_id'), fn ($q, $v) => $q->where('doctor_id', $v))
            ->when($request->query('room_id'), fn ($q, $v) => $q->where('room_id', $v))
            ->with(['patient', 'doctor', 'room', 'treatment'])
            ->orderBy('start_time')
            ->get();

        $doctors = Doctor::active()->orderBy('name')->get();
        $rooms = Room::active()->orderBy('name')->get();
        $patients = Patient::orderBy('full_name')->pluck('full_name', 'id');

        return view('appointments.index', compact('appointments', 'date', 'doctors', 'rooms', 'bookFor','patients'));
    }

    /**
     * Cross-field search across patient name/phone, doctor, room,
     * date range, visit type, and status.
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->query('q'));

                $q->whereHas('patient', fn ($p) => $p->where('full_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%"));
            })
            ->when($request->filled('doctor_id'), fn ($q, $v) => $q->where('doctor_id', $v))
            ->when($request->filled('room_id'), fn ($q, $v) => $q->where('room_id', $v))
            ->when($request->filled('visit_type'), fn ($q, $v) => $q->where('visit_type', $v))
            ->when($request->filled('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->filled('date_from'), fn ($q, $v) => $q->whereDate('appointment_date', '>=', $v))
            ->when($request->filled('date_to'), fn ($q, $v) => $q->whereDate('appointment_date', '<=', $v))
            ->with(['patient', 'doctor', 'room'])
            ->orderByDesc('appointment_date')
            ->paginate(20)
            ->withQueryString();

        $doctors = Doctor::active()->orderBy('name')->get();
        $rooms = Room::active()->orderBy('name')->get();

        return view('appointments.search', compact('appointments', 'doctors', 'rooms'));
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        $appointment->load(['patient', 'doctor', 'room', 'treatment']);

        return view('appointments.show', compact('appointment'));
    }

    public function store(AppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['status'] = $data['status'] ?? Appointment::STATUS_SCHEDULED;

        $appointment = DB::transaction(function () use ($data) {
            $this->abortIfConflicting($data);

            return Appointment::create($data);
        });

        return $this->respondSuccess(
            $request,
            __('messages.appointment_created'),
            'appointments.index',
            ['date' => $appointment->appointment_date->toDateString()]
        );
    }

    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $data = $request->validated();

        DB::transaction(function () use ($data, $appointment) {
            $this->abortIfConflicting($data, ignoreId: $appointment->id);

            $appointment->update($data);
        });

        return $this->respondSuccess(
            $request,
            __('messages.appointment_updated'),
            'appointments.index',
            ['date' => $appointment->appointment_date->toDateString()]
        );
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        $date = $appointment->appointment_date->toDateString();
        $appointment->delete();

        return $this->respondSuccess($request, __('messages.appointment_deleted'), 'appointments.index', ['date' => $date]);
    }

    /**
     * The FormRequest already caught the common case with a friendly
     * per-field error before this ever runs. This is the same check
     * re-run inside the caller's transaction, with a row lock, as the
     * actual atomicity guarantee against a genuine concurrent race -
     * two requests passing the unlocked pre-check in the same instant.
     */
    private function abortIfConflicting(array $data, ?int $ignoreId = null): void
    {
        $conflict = Appointment::findConflict(
            doctorId: (int) $data['doctor_id'],
            roomId: (int) $data['room_id'],
            date: $data['appointment_date'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            ignoreId: $ignoreId,
            lock: true,
        );

        if ($conflict) {
            throw ValidationException::withMessages([
                'appointment_date' => __('messages.appointment_conflict', [
                    'patient' => $conflict->patient->full_name,
                    'doctor' => $conflict->doctor->name,
                    'room' => $conflict->room->name,
                    'start' => $conflict->start_time,
                    'end' => $conflict->end_time,
                ]),
            ]);
        }
    }
}
