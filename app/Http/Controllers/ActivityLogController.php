<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Activity::class);

        $activities = Activity::with('causer')
            ->when($request->query('log_name'), fn ($q, $v) => $q->where('log_name', $v))
            ->when($request->query('causer_id'), fn ($q, $v) => $q->where('causer_id', $v))
            ->when($request->query('event'), fn ($q, $v) => $q->where('event', $v))
            ->when($request->query('date_from'), fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->query('date_to'), fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($query) use ($term) {
                    $query->where('description', 'like', "%{$term}%")
                        ->orWhereHas('causer', fn ($sub) => $sub->where('name', 'like', "%{$term}%"))
                        ->orWhere('log_name', 'like', "%{$term}%")
                        ->orWhere('event', 'like', "%{$term}%")
                        ->orWhere('subject_type', 'like', "%{$term}%");

                    if (is_numeric($term)) {
                        $query->orWhere('causer_id', $term);
                    }
                });
            })
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        // Normalize activity properties to arrays if stored as JSON string
        $activities->getCollection()->transform(function($activity) {
            if (is_string($activity->properties)) {
                $decoded = json_decode($activity->properties, true);
                $activity->properties = $decoded ?? [];
            }
            return $activity;
        });

        // Staff list for the "who" filter dropdown - small table, cheap
        // to load in full regardless of clinic size.
        $staff = User::orderBy('name')->get(['id', 'name']);

        $logNames = Activity::query()->whereNotNull('log_name')
            ->distinct()->orderBy('log_name')->pluck('log_name');

        return view('activity-log.index', compact('activities', 'staff', 'logNames'));
    }
}
