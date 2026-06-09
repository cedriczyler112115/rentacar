<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedRenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BannedRenterController extends Controller
{
    public function index(Request $request)
    {
        $query = BannedRenter::with('creator')->latest();

        if ($request->has('q') && $request->q != '') {
            $query->where('fullname', 'like', '%' . $request->q . '%');
        }

        $bannedRenters = $query->paginate(15);
        $q = $request->q;

        return view('admin.banned-renters.index', compact('bannedRenters', 'q'));
    }

    public function create()
    {
        return view('admin.banned-renters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'banned_details' => 'required|string',
            'id_presented' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = $request->file('id_presented')->store('banned_renters', 'public');

        BannedRenter::create([
            'fullname' => $request->fullname,
            'banned_details' => $request->banned_details,
            'id_presented' => $imagePath,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.banned-renters.index')->with('success', 'Banned Renter successfully added.');
    }

    public function edit(BannedRenter $bannedRenter)
    {
        return view('admin.banned-renters.edit', compact('bannedRenter'));
    }

    public function update(Request $request, BannedRenter $bannedRenter)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'banned_details' => 'required|string',
            'id_presented' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'fullname' => $request->fullname,
            'banned_details' => $request->banned_details,
        ];

        if ($request->hasFile('id_presented')) {
            if ($bannedRenter->id_presented && Storage::disk('public')->exists($bannedRenter->id_presented)) {
                Storage::disk('public')->delete($bannedRenter->id_presented);
            }
            $data['id_presented'] = $request->file('id_presented')->store('banned_renters', 'public');
        }

        $bannedRenter->update($data);

        return redirect()->route('admin.banned-renters.index')->with('success', 'Banned Renter updated successfully.');
    }

    public function destroy(BannedRenter $bannedRenter)
    {
        if ($bannedRenter->id_presented && Storage::disk('public')->exists($bannedRenter->id_presented)) {
            Storage::disk('public')->delete($bannedRenter->id_presented);
        }
        
        $bannedRenter->delete();

        return redirect()->route('admin.banned-renters.index')->with('success', 'Banned Renter deleted successfully.');
    }
}
