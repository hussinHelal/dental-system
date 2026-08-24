<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Room;
use App\Models\Treatment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    use RespondsToModals;

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
    $treatments = Treatment::active()->orderBy('name')->get();
    $bookForName = $bookFor ? Patient::find($bookFor)?->full_name : null;

    return view('appointments.index', compact(
        'appointments', 'date', 'doctors', 'rooms', 'bookFor', 'patients', 'treatments', 'bookForName'
    ));
}


   public function search(Request $request)
{
    $this->authorize('viewAny', Appointment::class);

    $appointments = Appointment::query()
        ->when($request->filled('q'), function ($query) use ($request) {
            $term = trim($request->query('q'));

            $query->where(function ($subQuery) use ($term) {

                if (is_numeric($term)) {
                    // Search purely by Patient ID or exact phone number
                    $subQuery->whereHas('patient', function ($p) use ($term) {
                        $p->where('id', $term)
                          ->orWhere('phone', $term);
                    });
                } else {
                    // Search text in Patient Name, Partial Phone, or Room Name
                    $subQuery->whereHas('patient', function ($p) use ($term) {
                        $p->where('full_name', 'like', "%{$term}%")
                          ->orWhere('phone', 'like', "%{$term}%");
                    })
                    ->orWhereHas('room', function ($r) use ($term) {
                        $r->where('name', 'like', "%{$term}%");
                    });
                }

            });
        })
        ->when($request->filled('patient_id'), fn ($q, $v) => $q->where('patient_id', $v))
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
    $rooms   = Room::active()->orderBy('name')->get();

    return view('appointments.search', compact('appointments', 'doctors', 'rooms'));
}

    public function availability(Request $request)
    {
        $request->validate([
            'appointment_date' => ['required', 'date'],
            'doctor_id'        => ['nullable', 'exists:doctors,id'],
            'room_id'          => ['nullable', 'exists:rooms,id'],
        ]);

        $query = Appointment::query()
            ->whereDate('appointment_date', $request->query('appointment_date'))
            ->whereNotIn('status', [Appointment::STATUS_CANCELLED, Appointment::STATUS_NO_SHOW]);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->query('doctor_id'));
        }
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->query('room_id'));
        }

        $appointments = $query->orderBy('start_time')->get(['start_time', 'end_time']);

        return response()->json(['appointments' => $appointments]);
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
                    'doctor'  => $conflict->doctor->name,
                    'room'    => $conflict->room->name,
                    'start'   => $conflict->start_time,
                    'end'     => $conflict->end_time,
                ]),
            ]);
        }
    }

    public function quickPatient(Request $request)
    {
    $this->authorize('create', Patient::class);

    $validated = $request->validate([
        'full_name' => ['required', 'string', 'max:150'],
        'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s]{7,30}$/'],
        'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
        'age' => ['nullable', 'integer', 'min:0', 'max:130'],
        'gender' => ['nullable', 'in:male,female'],
        'address' => ['nullable', 'string'],
    ]);

    $validated['created_by'] = $request->user()->id;

    $patient = Patient::create($validated);

    return response()->json([
        'success' => true,
        'patient' => [
            'id' => $patient->id,
            'full_name' => $patient->full_name,
            'phone' => $patient->phone,
        ],
        'message' => __('messages.patient_created'),
        'redirect' => route('appointments.index', ['book_for' => $patient->id]),
    ]);

    }

    public function randomPatient(Request $request)
    {
        $this->authorize('create', Patient::class);
    
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'confirm_duplicate' => ['nullable', 'boolean'],
        ]);
    
        $fullName = trim($validated['full_name']);
    
        // بحث دقيق عن الاسم باللغة العربية والإنجليزية مع تجاهل المسافات الزائدة
        $existing = Patient::where('full_name', $fullName)
            ->select('id', 'full_name', 'phone')
            ->first();
    
        // التحقق من وجود مريض بنفس الاسم تماماً
        if ($existing && ! $request->boolean('confirm_duplicate')) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'existing_patient' => [
                    'id' => $existing->id,
                    'full_name' => $existing->full_name,
                    'phone' => $existing->phone,
                ],
                'message' => __('messages.patient_duplicate_warning', ['name' => $existing->full_name]),
            ], 409);
        }
    
        // إنشاء المريض الجديد
        $patient = Patient::create([
            'full_name' => $fullName,
            'created_by' => $request->user()->id,
        ]);
    
        return response()->json([
            'success' => true,
            'patient' => [
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'phone' => $patient->phone,
            ],
            'message' => __('messages.patient_created'),
        ]);
    }

}