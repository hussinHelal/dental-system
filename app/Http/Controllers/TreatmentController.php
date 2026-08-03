<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\TreatmentRequest;
use App\Models\Treatment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    use RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Treatment::class);

        $treatments = Treatment::search($request->query('q'))
            ->category($request->query('category'))
            ->when($request->boolean('multi_session', false), fn ($q) => $q->multiSession())
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $categories = Treatment::query()->whereNotNull('category')
            ->distinct()->pluck('category');

        return view('treatments.index', compact('treatments', 'categories'));
    }

    public function show(Treatment $treatment)
    {
        $this->authorize('view', $treatment);

        return view('treatments.show', compact('treatment'));
    }

    public function store(TreatmentRequest $request)
    {
        $this->authorize('create', Treatment::class);

        Treatment::create($request->validated());

        return $this->respondSuccess($request, __('messages.treatment_created'), 'treatments.index');
    }

    public function update(TreatmentRequest $request, Treatment $treatment)
    {
        $this->authorize('update', $treatment);

        $treatment->update($request->validated());

        return $this->respondSuccess($request, __('messages.treatment_updated'), 'treatments.index');
    }

    public function destroy(Request $request, Treatment $treatment)
    {
        $this->authorize('delete', $treatment);

        $treatment->update(['is_active' => false]);

        return $this->respondSuccess($request, __('messages.treatment_deactivated'), 'treatments.index');
    }

    public function reactivate(Request $request, Treatment $treatment)
    {
        $this->authorize('update', $treatment);

        $treatment->update(['is_active' => true]);

        return $this->respondSuccess($request, __('messages.treatment_reactivated'), 'treatments.index');
    }
}
