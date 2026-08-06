<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Controllers\Concerns\RespondsToModals;
use App\Http\Requests\StaffRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    use HandlesImageUploads, RespondsToModals;

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $staff = User::role(User::ROLE_RECEPTIONIST)
            ->when($request->query('q'), fn ($q, $term) => $q->where(function ($qq) use ($term) {
                $qq->where('name', 'like', "%{$term}%")
                   ->orWhere('username', 'like', "%{$term}%");
            }))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('staff'));
    }
    public function create()
    {
        $this->authorize('create', User::class);

        return view('users.create');
    }

    public function store(StaffRequest $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'working_hours' => array_filter($data['working_hours'] ?? [], fn ($value) => filled($value)) ?: null,
        ]);

        if ($request->hasFile('photo')) {
            $user->update(['avatar' => $this->storeResizedImage($request->file('photo'), 'avatars')]);
        }

        $user->assignRole(User::ROLE_RECEPTIONIST);

        return redirect()->route('users.index')->with('success', __('messages.staff_created'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    public function update(StaffRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validated();

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->working_hours = array_filter($data['working_hours'] ?? [], fn ($value) => filled($value)) ?: null;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $this->storeResizedImage($request->file('photo'), 'avatars');
        }

        $user->save();

        return redirect()->route('users.index')->with('success', __('messages.staff_updated'));
    }

    /**
     * Soft toggle only - staff accounts are never hard-deleted.
     */
    public function toggleActive(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update(['is_active' => ! $user->is_active]);

        return $this->respondSuccess($request, __('messages.staff_updated'), 'users.index');
    }
}
