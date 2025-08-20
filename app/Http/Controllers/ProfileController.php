<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use App\Models\Subprofile;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->loadMissing('subprofiles');
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['nullable','string','max:190'],
            'username' => ['required','string','max:190','unique:users,username,'.$user->id],
            'email'    => ['required','email','max:190','unique:users,email,'.$user->id],
        ]);

        $user->fill($data)->save();

        return back()->with('message', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        if ($user->avatar_path ?? null) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar_path = $path;
        $user->save();

        return back()->with('message', 'Avatar updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required','current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Your User model casts 'password' => 'hashed', so plain assignment is fine.
        $user->password = $request->password;
        $user->save();

        return back()->with('message', 'Password changed successfully.');
    }

    /* ------- Optional: lightweight sub-profiles ------- */

    public function storeSubprofile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'label' => ['required','string','max:120'],
            'bio'   => ['nullable','string','max:1000'],
        ]);

        $sp = $user->subprofiles()->create($data);

        if (!$user->subprofiles()->where('is_default', true)->exists()) {
            $sp->is_default = true;
            $sp->save();
        }

        return back()->with('message', 'Profile added.');
    }

    public function destroySubprofile(Request $request, Subprofile $subprofile)
    {
        abort_if($subprofile->user_id !== $request->user()->id, 403);

        $wasDefault = $subprofile->is_default;
        $uid = $subprofile->user_id;
        $subprofile->delete();

        if ($wasDefault) {
            $next = Subprofile::where('user_id', $uid)->first();
            if ($next) { $next->is_default = true; $next->save(); }
        }

        return back()->with('message', 'Profile removed.');
    }

    public function makeDefault(Request $request, Subprofile $subprofile)
    {
        abort_if($subprofile->user_id !== $request->user()->id, 403);

        Subprofile::where('user_id', $subprofile->user_id)->update(['is_default' => false]);
        $subprofile->is_default = true;
        $subprofile->save();

        return back()->with('message', 'Default profile updated.');
    }

    public function updateSocial(Request $request)
    {
        $rules = [
            'website'   => ['nullable','url','max:255'],
            'facebook'  => ['nullable','url','max:255'],
            'twitter'   => ['nullable','url','max:255'],
            'youtube'   => ['nullable','url','max:255'],
            'wordpress' => ['nullable','url','max:255'],
            'instagram' => ['nullable','url','max:255'],
            'quora'     => ['nullable','url','max:255'],
            'pinterest' => ['nullable','url','max:255'],
            'linkedin'  => ['nullable','url','max:255'],
            'blogger'   => ['nullable','url','max:255'],
        ];

        $data = $request->validate($rules);

        // Keep only non-empty, trimmed values
        $clean = array_filter(array_map('trim', $data), fn($v) => filled($v));

        $user = $request->user();
        $user->social_links = $clean;
        $user->save();

        return back()->with('message', 'Social links updated.');
    }
}
