<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use RespondsToModals;

   public function index(Request $request)
    {
        $this->authorize('viewAny', Room::class);

        $rooms = Room::search($request->query('q'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function store(RoomRequest $request)
    {
        $this->authorize('create', Room::class);

        Room::create($request->validated());

        return $this->respondSuccess($request, __('messages.room_created'), 'rooms.index');
    }

    public function update(RoomRequest $request, Room $room)
    {
        $this->authorize('update', $room);

        $room->update($request->validated());

        return $this->respondSuccess($request, __('messages.room_updated'), 'rooms.index');
    }

    public function destroy(Request $request, Room $room)
    {
        $this->authorize('delete', $room);

        $room->update(['is_active' => false]);

        return $this->respondSuccess($request, __('messages.room_deactivated'), 'rooms.index');
    }

    public function reactivate(Request $request, Room $room)
    {
        $this->authorize('update', $room);

        $room->update(['is_active' => true]);

        return $this->respondSuccess($request, __('messages.room_reactivated'), 'rooms.index');
    }
}
