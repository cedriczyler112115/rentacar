<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarwashServicePayment;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CarwashServicePaymentsController extends Controller
{
    public function index(Request $request): View
    {
        $payments = CarwashServicePayment::query()
            ->with(['user', 'vehicle'])
            ->orderByDesc('service_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $vehicles = Vehicle::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->orderBy('license_plate')
            ->get(['id', 'name', 'license_plate']);

        return view('admin.carwash-service-payments.index', compact('payments', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'service_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'vehicle_proof' => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('vehicle_proof')->store('carwash-service-payments', 'public');

        $payment = CarwashServicePayment::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $validated['vehicle_id'],
            'service_date' => $validated['service_date'],
            'amount_paid' => $validated['amount_paid'],
            'vehicle_proof_path' => $path,
        ]);

        $this->sendTelegramMessage($payment, 'created');

        return redirect()->route('admin.carwash-service-payments.index')->with('success', 'Carwash service payment added successfully.');
    }

    public function update(Request $request, CarwashServicePayment $payment)
    {
        $validated = $request->validate([
            'vehicle_id' => [
                'required',
                Rule::exists('vehicles', 'id')->where(fn ($q) => $q->where('user_id', Auth::id())),
            ],
            'service_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0',
            'vehicle_proof' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        $oldPath = $payment->vehicle_proof_path;
        if ($request->hasFile('vehicle_proof')) {
            $payment->vehicle_proof_path = $request->file('vehicle_proof')->store('carwash-service-payments', 'public');
        }

        $payment->service_date = $validated['service_date'];
        $payment->amount_paid = $validated['amount_paid'];
        $payment->vehicle_id = $validated['vehicle_id'];
        $payment->save();

        $this->sendTelegramMessage($payment, 'updated');

        if ($request->hasFile('vehicle_proof') && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.carwash-service-payments.index')->with('success', 'Carwash service payment updated successfully.');
    }

    private function sendTelegramMessage(CarwashServicePayment $payment, $action)
    {
        $telegramToken = '8555688646:AAFRitSezZXmTSeXtSxpLOK1BLHQ1qyE-KE';
        
        $chatId = '-1003711130933';
        
        $message = "Carwash Service Payment has been successfully {$action}.\n"
                 . "Payment ID: {$payment->id}\n"
                 . "Vehicle ID: {$payment->vehicle_id}\n"
                 . "Service Date: {$payment->service_date}\n"
                 . "Amount Paid: {$payment->amount_paid}\n\n"
                 . "📍View location:\nhttps://www.google.com/maps?q=8.94726263776974,125.51468795811796";
                 
        try {
            $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            if ($response->failed()) {
                \Illuminate\Support\Facades\Log::error('Telegram API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Telegram Exception: ' . $e->getMessage());
        }
    }

    public function destroy(CarwashServicePayment $payment)
    {
        $path = $payment->vehicle_proof_path;
        $payment->delete();

        if ($path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('admin.carwash-service-payments.index')->with('success', 'Carwash service payment deleted successfully.');
    }
}
