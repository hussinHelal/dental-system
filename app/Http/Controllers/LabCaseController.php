<?php

namespace App\Http\Controllers;

use App\Models\DentalLab;
use App\Models\LabCase;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LabCaseController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:sent,in_progress,received,delivered'],
            'dental_lab_id' => ['nullable', 'integer', 'exists:dental_labs,id'],
        ]);

        $cases = LabCase::query()
            ->select([
                'id', 'dental_lab_id', 'patient_id', 'case_type', 'sent_date',
                'expected_return_date', 'actual_return_date', 'cost', 'status', 'notes', 'created_by',
            ])
            ->with([
                'lab:id,name',
                'patient:id,full_name,phone',
                'creator:id,name',
            ])
            ->when(isset($filters['status']), fn ($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(isset($filters['dental_lab_id']), fn ($q) =>
                $q->where('dental_lab_id', $filters['dental_lab_id'])
            )
            ->orderByDesc('sent_date')
            ->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        $labs = DentalLab::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('lab-cases.index', compact('cases', 'labs'));
    }

    public function patientLookup(Request $request)
    {
        $query = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ])['q'];

        $patients = Patient::query()
            ->select(['id', 'full_name', 'phone'])
            ->where(function ($builder) use ($query) {
                $builder
                    ->where('full_name', 'like', '%'.$query.'%')
                    ->orWhere('phone', 'like', '%'.$query.'%');
            })
            ->orderBy('full_name')
            ->limit(8)
            ->get()
            ->map(fn (Patient $patient) => [
                'id' => $patient->id,
                'name' => $patient->full_name,
                'phone' => $patient->phone,
            ]);

        return response()->json(['data' => $patients]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        LabCase::create($data);

        return back()->with('success', __('dental_labs.case_created'));
    }

    public function update(Request $request, LabCase $labCase): RedirectResponse
    {
        $labCase->update($this->validated($request));

        return back()->with('success', __('dental_labs.case_updated'));
    }

    public function destroy(LabCase $labCase): RedirectResponse
    {
        $labCase->delete();

        return back()->with('success', __('dental_labs.case_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'dental_lab_id' => ['required', 'integer', 'exists:dental_labs,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'case_type' => ['required', 'string', 'max:150'],
            'sent_date' => ['required', 'date', 'before_or_equal:today'],
            'expected_return_date' => ['nullable', 'date', 'after_or_equal:sent_date'],
            'actual_return_date' => ['nullable', 'date', 'after_or_equal:sent_date'],
            'cost' => ['required', 'decimal:0,2', 'numeric', 'min:0', 'max:99999999.99'],
            'status' => ['required', 'in:sent,in_progress,received,delivered'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
