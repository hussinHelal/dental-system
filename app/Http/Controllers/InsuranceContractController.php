<?php

namespace App\Http\Controllers;

use App\Models\InsuranceContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsuranceContractController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,expired,cancelled'],
        ]);

        $search = isset($filters['search']) ? trim($filters['search']) : null;

        $contracts = InsuranceContract::query()
            ->select([
                'id', 'company_name', 'contract_number', 'start_date', 'end_date',
                'coverage_details', 'discount_percentage', 'contact_person', 'phone',
                'status', 'notes', 'created_by',
            ])
            ->with('creator:id,name')
            ->when($search !== null && $search !== '', function ($q) use ($search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('company_name', 'like', '%'.$search.'%')
                        ->orWhere('contract_number', 'like', '%'.$search.'%');
                });
            })
            ->when(isset($filters['status']), fn ($q) =>
                $q->where('status', $filters['status'])
            )
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('insurance.index', compact('contracts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        InsuranceContract::create($data);

        return back()->with('success', __('insurance.created_successfully'));
    }

    public function update(Request $request, InsuranceContract $insurance): RedirectResponse
    {
        $insurance->update($this->validated($request));

        return back()->with('success', __('insurance.updated_successfully'));
    }

    public function destroy(InsuranceContract $insurance): RedirectResponse
    {
        $insurance->delete();

        return back()->with('success', __('insurance.deleted_successfully'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'contract_number' => ['nullable', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'coverage_details' => ['nullable', 'string', 'max:2000'],
            'discount_percentage' => ['required', 'decimal:0,2', 'numeric', 'min:0', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'status' => ['required', 'in:active,expired,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
