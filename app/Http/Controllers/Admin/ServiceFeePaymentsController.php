<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceFeePayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ServiceFeePaymentsController extends Controller
{
    public function index(Request $request): View
    {
        $baseYears = range((int) now()->year + 1, (int) now()->year - 5);
        $yearsFromData = ServiceFeePayment::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->all();
        $yearOptions = array_values(array_unique(array_merge($yearsFromData, $baseYears)));
        rsort($yearOptions);

        $query = ServiceFeePayment::query()
            ->with(['user' => fn ($q) => $q->withCount('vehicles')])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderByDesc('id');

        $year = $request->filled('year') ? (int) $request->query('year') : null;
        $month = $request->filled('month') ? (int) $request->query('month') : null;

        if ($year) {
            $query->where('year', $year);
        }
        if ($month) {
            $query->where('month', $month);
        }

        $payments = $query->paginate(10)->withQueryString();
        $monthNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];

        return view('admin.service-fee-payments.index', compact('payments', 'monthNames', 'year', 'month', 'yearOptions'));
    }

    public function members(Request $request): View
    {
        $baseYears = range((int) now()->year + 1, (int) now()->year - 5);
        $yearsFromData = ServiceFeePayment::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y)
            ->all();
        $yearOptions = array_values(array_unique(array_merge($yearsFromData, $baseYears)));
        rsort($yearOptions);

        $year = $request->filled('year') ? (int) $request->query('year') : (int) now()->year;
        $month = $request->filled('month') ? (int) $request->query('month') : (int) now()->month;
        $status = (string) $request->query('status', 'all');

        $paidExists = function ($q) use ($year, $month) {
            $q->selectRaw('1')
                ->from('service_fee_payments')
                ->whereColumn('service_fee_payments.user_id', 'users.id')
                ->where('year', $year)
                ->where('month', $month);
        };

        $users = User::query()
            ->where('is_aaracc', true)
            ->withCount('vehicles')
            ->withExists([
                'serviceFeePayments as has_paid' => function ($q) use ($year, $month) {
                    $q->where('year', $year)->where('month', $month);
                },
            ])
            ->when($status === 'paid', fn ($q) => $q->whereExists($paidExists))
            ->when($status === 'unpaid', fn ($q) => $q->whereNotExists($paidExists))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $monthNames = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];

        return view('admin.service-fee-payments.members', compact('users', 'year', 'month', 'status', 'monthNames', 'yearOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('proof')->store('service-fee-payments', 'public');

        ServiceFeePayment::create([
            'user_id' => Auth::id(),
            'year' => $validated['year'],
            'month' => $validated['month'],
            'amount' => $validated['amount'],
            'proof_path' => $path,
        ]);

        return redirect()->route('admin.service-fee-payments.index')->with('success', 'Payment added successfully.');
    }

    public function update(Request $request, ServiceFeePayment $payment)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $oldPath = $payment->proof_path;
        if ($request->hasFile('proof')) {
            $payment->proof_path = $request->file('proof')->store('service-fee-payments', 'public');
        }

        $payment->year = $validated['year'];
        $payment->month = $validated['month'];
        $payment->amount = $validated['amount'];
        $payment->save();

        if ($request->hasFile('proof') && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.service-fee-payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(ServiceFeePayment $payment)
    {
        $path = $payment->proof_path;
        $payment->delete();

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.service-fee-payments.index')->with('success', 'Payment deleted successfully.');
    }
}
