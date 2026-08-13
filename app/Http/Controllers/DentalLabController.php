<?php

namespace App\Http\Controllers;

use App\Models\DentalLab;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DentalLabController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = isset($filters['search']) ? trim($filters['search']) : null;

        $labs = DentalLab::query()
            ->select(['id', 'name', 'phone', 'address', 'notes'])
            ->when($search !== null && $search !== '', fn ($q) =>
                $q->where('name', 'like', '%'.$search.'%')
            )
            ->withCount('cases')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('dental-labs.index', compact('labs'));
    }

    public function store(Request $request): RedirectResponse
    {
        DentalLab::create($this->validated($request));

        return back()->with('success', __('dental_labs.lab_created'));
    }

    public function update(Request $request, DentalLab $dentalLab): RedirectResponse
    {
        $dentalLab->update($this->validated($request));

        return back()->with('success', __('dental_labs.lab_updated'));
    }

    public function destroy(DentalLab $dentalLab): RedirectResponse
    {
        $dentalLab->delete();

        return back()->with('success', __('dental_labs.lab_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
