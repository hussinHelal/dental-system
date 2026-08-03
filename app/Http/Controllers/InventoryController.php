<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\InventoryItemRequest;
use App\Http\Requests\InventoryQuantityRequest;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    use HandlesImageUploads, RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', InventoryItem::class);

        $items = InventoryItem::search($request->query('q'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('inventory.index', compact('items'));
    }

    public function store(InventoryItemRequest $request)
    {
        $this->authorize('create', InventoryItem::class);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'inventory');
        }

        InventoryItem::create($data);

        return $this->respondSuccess($request, __('messages.item_created'), 'inventory.index');
    }

    /**
     * Full edit - Doctor only (enforced by the 'inventory.full' route
     * middleware group in routes/web.php, plus the policy below).
     */
    public function update(InventoryItemRequest $request, InventoryItem $item)
    {
        $this->authorize('update', $item);

        if (! $request->user()->isDoctor()) {
            abort(403);
        }

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }
            $data['photo'] = $this->storeResizedImage($request->file('photo'), 'inventory');
        }

        $item->update($data);

        return $this->respondSuccess($request, __('messages.item_updated'), 'inventory.index');
    }

    /**
     * Quantity-only update - the one write action a Receptionist may
     * perform on inventory.
     */
    public function updateQuantity(InventoryQuantityRequest $request, InventoryItem $item)
    {
        $this->authorize('update', $item);

        $item->update(['quantity' => $request->validated('quantity')]);

        return $this->respondSuccess($request, __('messages.item_updated'), 'inventory.index');
    }

    public function destroy(Request $request, InventoryItem $item)
    {
        $this->authorize('delete', $item);

        if ($item->photo) {
            Storage::disk('public')->delete($item->photo);
        }
        $item->delete();

        return $this->respondSuccess($request, __('messages.item_deleted'), 'inventory.index');
    }
}
