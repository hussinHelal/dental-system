<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class AgendaController extends Controller
{
   

    public function index(Request $request): View
    {
        $date = $this->resolveDate($request->input('date'));

        $doctors = collect();
        $loadError = null;

        try {
            $doctors = Doctor::orderBy('name')->get(['id', 'name']);
        } catch (Throwable $e) {
            report($e);
            $loadError = __('agenda.load_failed');
        }

        $groupedAppointments = collect();

        if (! $loadError) {
            try {
                $groupedAppointments = $this->appointmentsForDate($date, $request->input('doctor_id'))
                    ->groupBy(fn ($appointment) => $appointment->doctor_id ?? 'unassigned');
            } catch (Throwable $e) {
                report($e);
                $loadError = __('agenda.load_failed');
            }
        }

        return view('agenda.index', [
            'date' => $date,
            'doctors' => $doctors,
            'groupedAppointments' => $groupedAppointments,
            'loadError' => $loadError,
            'selectedDoctorId' => $request->input('doctor_id'),
        ]);
    }

    /**
     * AJAX endpoint used for date navigation without a full page reload.
     * Always returns JSON, including on failure — the front end never has to
     * guess whether a non-200 response is HTML (a Laravel error page) or JSON.
     */
    public function data(Request $request): JsonResponse
    {
        $date = $this->resolveDate($request->input('date'));

        try {
            $appointments = $this->appointmentsForDate($date, $request->input('doctor_id'));

            $payload = [
                'date' => $date,
                'appointments' => $appointments->map(fn (Appointment $appointment) => [
                    'id' => $appointment->id,
                    'patient_name' => $this->patientName($appointment),
                    'doctor_id' => $appointment->doctor_id,
                    'doctor_name' => $appointment->doctor?->name,
                    'room_name' => $appointment->room?->name,
                    'start_time' => $this->formatTime($appointment->start_time),
                    'end_time' => $this->formatTime($appointment->end_time),
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                ])->values(),
            ];
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => __('agenda.load_failed'),
            ], 500);
        }

        return response()->json($payload);
    }

    private function appointmentsForDate(string $date, ?string $doctorId)
    {
        return Appointment::query()
            ->with(['patient', 'doctor:id,name', 'room:id,name'])
            ->whereDate('appointment_date', $date)
            ->when(filled($doctorId), fn ($query) => $query->where('doctor_id', $doctorId))
            ->orderBy('start_time')
            ->get();
    }

    // Adjust to your actual Patient model if the column differs — the rest of this
    // package assumes `full_name` (confirmed from the host app's own patient search).
    private function patientName(Appointment $appointment): string
    {
        return $appointment->patient?->full_name
            ?? $appointment->patient?->name
            ?? __('agenda.unknown_patient');
    }

    private function formatTime(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i');
        }

        try {
            return Carbon::parse((string) $value)->format('H:i');
        } catch (Throwable) {
            return (string) $value;
        }
    }

    /** Strict Y-m-d parsing with a same-day fallback — never trusts raw input into a date query. */
    private function resolveDate(mixed $value): string
    {
        if (is_string($value) && $value !== '') {
            try {
                $parsed = Carbon::createFromFormat('!Y-m-d', $value);
                if ($parsed && $parsed->format('Y-m-d') === $value) {
                    return $value;
                }
            } catch (Throwable) {
                // fall through to today
            }
        }

        return now()->toDateString();
    }
}
