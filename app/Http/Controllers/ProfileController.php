<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesImageUploads;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use HandlesImageUploads;

    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        $user->name = $data['name'];

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

        return back()->with('success', __('messages.profile_updated'));
    }

    public function updateTheme(Request $request)
    {
        $request->validate(['theme' => 'required|in:light,dark']);

        // Stored on the user record (not localStorage) so it follows
        // the user across devices/browsers.
        $request->user()->update(['theme' => $request->input('theme')]);

        return response()->json(['success' => true]);
    }

    public function updateLocale(Request $request)
    {
        $request->validate(['locale' => 'required|in:en,ar']);

        $request->session()->put('locale', $request->input('locale'));

        return response()->json(['success' => true]);
    }
}
