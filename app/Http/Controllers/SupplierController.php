<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = isset($filters['search']) ? trim($filters['search']) : null;

        $suppliers = Supplier::query()
            ->select(['id', 'name', 'phone', 'address', 'notes'])
            ->when($search !== null && $search !== '', fn ($q) =>
                $q->where('name', 'like', '%'.$search.'%')
            )
            ->withCount('purchases')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return back()->with('success', __('suppliers.created_successfully'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));

        return back()->with('success', __('suppliers.updated_successfully'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return back()->with('success', __('suppliers.deleted_successfully'));
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
