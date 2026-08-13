<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    private const PAGE_SIZE = 20;
    private const MAX_MONEY = 99999999.99;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'payment_status' => ['nullable', 'in:paid,partial,unpaid'],
        ]);

        $purchases = Purchase::query()
            ->select([
                'id', 'supplier_id', 'purchase_date', 'total_amount',
                'payment_status', 'notes', 'created_by',
            ])
            ->with([
                'supplier:id,name',
                'creator:id,name',
                'items:id,purchase_id,item_name,quantity,unit_price,subtotal',
            ])
            ->when(isset($filters['supplier_id']), fn ($q) =>
                $q->where('supplier_id', $filters['supplier_id'])
            )
            ->when(isset($filters['payment_status']), fn ($q) =>
                $q->where('payment_status', $filters['payment_status'])
            )
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        $suppliers = Supplier::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $request): void {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'payment_status' => $data['payment_status'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
                'total_amount' => 0,
            ]);

            $this->syncItems($purchase, $data['items']);
            $purchase->recalculateTotal();
        });

        return back()->with('success', __('purchases.created_successfully'));
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $purchase): void {
            $purchase->update([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'payment_status' => $data['payment_status'],
                'notes' => $data['notes'] ?? null,
            ]);

            $purchase->items()->delete();
            $this->syncItems($purchase, $data['items']);
            $purchase->recalculateTotal();
        });

        return back()->with('success', __('purchases.updated_successfully'));
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->delete();

        return back()->with('success', __('purchases.deleted_successfully'));
    }

    private function syncItems(Purchase $purchase, array $items): void
    {
        $timestamp = now();
        $rows = [];

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $subtotal = round($quantity * $unitPrice, 2);

            // The DB column is DECIMAL(10,2). Reject an individual line before
            // attempting the INSERT instead of allowing a database overflow.
            if ($subtotal > self::MAX_MONEY) {
                throw ValidationException::withMessages([
                    'items' => __('purchases.item_total_too_large'),
                ]);
            }

            $rows[] = [
                'purchase_id' => $purchase->id,
                'item_name' => trim($item['item_name']),
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        $purchase->items()->insert($rows);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_status' => ['required', 'in:paid,partial,unpaid'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.item_name' => ['required', 'string', 'max:150'],
            'items.*.quantity' => ['required', 'decimal:0,2', 'numeric', 'min:0.01', 'max:99999999.99'],
            'items.*.unit_price' => ['required', 'decimal:0,2', 'numeric', 'min:0', 'max:99999999.99'],
        ]);

        $total = 0.0;

        foreach ($data['items'] as $item) {
            $subtotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);

            if ($subtotal > self::MAX_MONEY) {
                throw ValidationException::withMessages([
                    'items' => __('purchases.item_total_too_large'),
                ]);
            }

            $total += $subtotal;

            if ($total > self::MAX_MONEY) {
                throw ValidationException::withMessages([
                    'items' => __('purchases.total_too_large'),
                ]);
            }
        }

        return $data;
    }
}
