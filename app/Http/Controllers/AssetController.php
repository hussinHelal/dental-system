<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class AssetController extends Controller
{
    private const PAGE_SIZE = 20;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = isset($filters['search']) ? trim($filters['search']) : null;

        $assets = Asset::query()
            ->select([
                'id', 'name', 'category', 'purchase_date', 'purchase_cost',
                'salvage_value', 'useful_life_years', 'notes', 'attachment_path', 'created_by',
            ])
            ->with('creator:id,name')
            ->when($search !== null && $search !== '', function ($q) use ($search): void {
                $q->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('assets.index', compact('assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $attachment = null;

        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('asset-attachments', 'public');
            $data['attachment_path'] = $attachment;
        }

        $data['created_by'] = $request->user()->id;

        try {
            Asset::create($data);
        } catch (Throwable $e) {
            if ($attachment !== null) {
                Storage::disk('public')->delete($attachment);
            }

            throw $e;
        }

        return back()->with('success', __('assets.created_successfully'));
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $data = $this->validated($request);
        $oldAttachment = $asset->attachment_path;
        $newAttachment = null;

        if ($request->hasFile('attachment')) {
            $newAttachment = $request->file('attachment')->store('asset-attachments', 'public');
            $data['attachment_path'] = $newAttachment;
        }

        try {
            $asset->update($data);
        } catch (Throwable $e) {
            if ($newAttachment !== null) {
                Storage::disk('public')->delete($newAttachment);
            }

            throw $e;
        }

        if ($newAttachment !== null && $oldAttachment !== null && $oldAttachment !== $newAttachment) {
            Storage::disk('public')->delete($oldAttachment);
        }

        return back()->with('success', __('assets.updated_successfully'));
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $attachment = $asset->attachment_path;

        // Delete the database row first. If the DB operation fails, the file is
        // retained. A failed file deletion after a successful DB delete only leaves
        // an orphaned attachment, which is safer than losing a referenced file.
        $asset->delete();

        if ($attachment !== null) {
            Storage::disk('public')->delete($attachment);
        }

        return back()->with('success', __('assets.deleted_successfully'));
    }

    private function validated(Request $request): array
    {
        $payload = $request->all();
        $payload['purchase_cost'] = $this->normalizeAmount($payload['purchase_cost'] ?? null);
        $payload['salvage_value'] = $this->normalizeAmount($payload['salvage_value'] ?? null);
        $request->replace($payload);

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'purchase_cost' => ['required', 'decimal:0,2', 'numeric', 'min:0', 'max:99999999.99'],
            'salvage_value' => ['required', 'decimal:0,2', 'numeric', 'min:0', 'lte:purchase_cost'],
            'useful_life_years' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);
    }

    private function normalizeAmount(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = strtolower(trim((string) $value));
        $value = str_replace(',', '', $value);
        $multiplier = 1;
        $suffix = substr($value, -1);

        if ($suffix === 'k' || $suffix === 'm' || $suffix === 'b') {
            $multiplier = match ($suffix) {
                'k' => 1000,
                'm' => 1000000,
                'b' => 1000000000,
            };
            $value = substr($value, 0, -1);
        }

        if (!preg_match('/^\d+(?:\.\d+)?$/', $value)) {
            return (string) $value;
        }

        return number_format((float) $value * $multiplier, 2, '.', '');
    }
}
