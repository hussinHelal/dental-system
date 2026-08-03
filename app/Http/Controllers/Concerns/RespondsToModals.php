<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait RespondsToModals
{
    protected function respondSuccess(Request $request, string $message, ?string $route = null, array $routeParams = [])
    {
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => $route ? route($route, $routeParams) : null,
            ]);
        }

        return $route
            ? redirect()->route($route, $routeParams)->with('success', $message)
            : back()->with('success', $message);
    }
}
