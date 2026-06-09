<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MemberCapital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberCapitalController extends Controller
{
    public function index()
    {
        $capitals = MemberCapital::with('user')->get();
        return view('admin.member-capitals.index', compact('capitals'));
    }

    public function create()
    {
        $users = User::where('is_aaracc', true)->get();
        return view('admin.member-capitals.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('is_aaracc', true)),
            ],
            'amount_added' => 'required|numeric|min:0',
            'date_invested' => 'required|date',
        ]);

        $existing = MemberCapital::where('user_id', $request->user_id)->first();

        if ($existing) {
            $amount = (float) $request->amount_added;
            $log = is_array($existing->capital_additions_log) ? $existing->capital_additions_log : [];
            $log[] = [
                'amount' => $amount,
                'date_added' => $request->date_invested,
                'created_by' => auth()->user()?->name ?? 'System',
            ];

            $existing->update([
                'current_capital' => (float) $existing->current_capital + $amount,
                'initial_capital' => (float) $existing->current_capital + $amount,
                'date_invested' => $request->date_invested,
                'status' => 'active',
                'capital_additions_log' => $log,
            ]);
        } else {
            $amount = (float) $request->amount_added;
            MemberCapital::create([
                'user_id' => $request->user_id,
                'initial_capital' => $amount,
                'current_capital' => $amount,
                'date_invested' => $request->date_invested,
                'status' => 'active',
                'capital_additions_log' => [
                    [
                        'amount' => $amount,
                        'date_added' => $request->date_invested,
                        'created_by' => auth()->user()?->name ?? 'System',
                    ],
                ],
            ]);
        }

        return redirect()->route('admin.member-capitals.index')->with('success', 'Member capital added successfully.');
    }

    public function edit(MemberCapital $memberCapital)
    {
        return view('admin.member-capitals.edit', compact('memberCapital'));
    }

    public function update(Request $request, MemberCapital $memberCapital)
    {
        $validated = $request->validate([
            'current_capital' => 'required|numeric|min:0',
            'date_invested' => 'required|date',
            'status' => 'required|in:active,withdrawn',
        ]);

        $log = is_array($memberCapital->capital_additions_log) ? $memberCapital->capital_additions_log : [];
        $sum = 0.0;
        foreach ($log as $item) {
            $sum += (float) ($item['amount'] ?? 0);
        }

        $target = (float) $validated['current_capital'];
        $diff = $target - $sum;

        if (abs($diff) > 0.01) {
            $log[] = [
                'amount' => $diff,
                'date_added' => $validated['date_invested'],
                'created_by' => auth()->user()?->name ?? 'System',
            ];
        }

        $memberCapital->update([
            'current_capital' => $target,
            'initial_capital' => $target,
            'date_invested' => $validated['date_invested'],
            'status' => $validated['status'],
            'capital_additions_log' => $log,
        ]);

        return redirect()->route('admin.member-capitals.index')->with('success', 'Member capital updated successfully.');
    }

    public function destroy(MemberCapital $memberCapital)
    {
        $memberCapital->delete();
        return redirect()->route('admin.member-capitals.index')->with('success', 'Member capital deleted successfully.');
    }
}
