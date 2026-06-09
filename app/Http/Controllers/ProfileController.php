<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\UserLegitimacyProof;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $legitimacyProofs = collect();
        if (Schema::hasTable('user_legitimacy_proofs')) {
            $legitimacyProofs = $request->user()->legitimacyProofs()->orderByDesc('id')->get();
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'legitimacyProofs' => $legitimacyProofs,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        if (!Schema::hasColumn('users', 'about_owner')) {
            unset($validated['about_owner']);
        }
        $user->fill($validated);

        if ($request->hasFile('profile_photo')) {
            $oldPath = $user->profile_photo_path;
            $newPath = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo_path = $newPath;
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $proofFiles = $request->file('legitimacy_proofs', []);
        if (is_array($proofFiles) && count($proofFiles) > 0) {
            $existingCount = Schema::hasTable('user_legitimacy_proofs') ? $user->legitimacyProofs()->count() : 0;
            if ($existingCount + count($proofFiles) > 15) {
                return back()->withErrors(['legitimacy_proofs' => 'You can upload up to 15 proof files in total.'])->withInput();
            }
        }

        $user->save();

        if (is_array($proofFiles) && count($proofFiles) > 0) {
            if (!Schema::hasTable('user_legitimacy_proofs')) {
                return Redirect::route('profile.edit')->withErrors(['legitimacy_proofs' => 'Proof uploads are not available until the database is migrated.']);
            }
            foreach ($proofFiles as $file) {
                if (!$file) {
                    continue;
                }
                $path = $file->store('legitimacy_proofs/' . $user->id, 'public');
                UserLegitimacyProof::create([
                    'user_id' => $user->id,
                    'file_path' => $path,
                ]);
            }
        }

        $intended = $request->session()->get('url.intended');
        $missingContact = !is_string($user->contact_number) || trim($user->contact_number) === '';
        $missingAddress = !is_string($user->address) || trim($user->address) === '';

        if (!$missingContact && !$missingAddress && is_string($intended) && $intended !== '' && !str_starts_with($intended, '/profile')) {
            return redirect()->intended(route('dashboard', absolute: false))->with('status', 'profile-updated');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroyLegitimacyProof(Request $request, UserLegitimacyProof $proof): RedirectResponse
    {
        if (!Schema::hasTable('user_legitimacy_proofs')) {
            abort(404);
        }

        if ((int) $proof->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        if (is_string($proof->file_path) && $proof->file_path !== '') {
            Storage::disk('public')->delete($proof->file_path);
        }
        $proof->delete();

        return Redirect::route('profile.edit')->with('status', 'proof-deleted');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
