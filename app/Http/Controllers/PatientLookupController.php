<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientLookupController extends Controller
{
    private const LIMIT = 8;

    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $patients = Patient::query()
            ->select(['id', 'name', 'phone'])
            ->where(function ($builder) use ($query): void {
                $builder->where('name', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%');
            })
            ->orderBy('name')
            ->limit(self::LIMIT)
            ->get();

        return response()->json(['data' => $patients]);
    }
}
