<?php

namespace App\Jobs;

use App\Mail\BookingCancelledOwnerMail;
use App\Mail\BookingCreatedOwnerMail;
use App\Models\BookingEmailLog;
use App\Models\Rental;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class SendBookingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function backoff(): array
    {
        return [10, 60, 300, 900, 1800];
    }

    public function __construct(
        public int $emailLogId,
    ) {
    }

    public function handle(): void
    {
        $logRow = BookingEmailLog::query()->find($this->emailLogId);
        if (!$logRow) {
            return;
        }

        $rateKey = 'booking-email:' . $logRow->type . ':' . sha1($logRow->to_email);
        if (RateLimiter::tooManyAttempts($rateKey, 6)) {
            $this->release(60);
            return;
        }
        RateLimiter::hit($rateKey, 60);

        $logRow->update(['attempts' => (int) $logRow->attempts + 1]);

        $rental = Rental::query()
            ->with([
                'user',
                'vehicle.user',
                'vehicle.libBrand',
                'vehicle.libType',
                'vehicle.libTransmission',
                'vehicle.libFuelType',
            ])
            ->find($logRow->rental_id);

        if (!$rental || !$rental->vehicle || !$rental->vehicle->user) {
            $logRow->update([
                'status' => 'failed',
                'error_message' => 'Rental/vehicle/owner not found.',
            ]);
            return;
        }

        try {
            if ($logRow->type === 'booking_created_owner') {
                Mail::to($logRow->to_email)->send(new BookingCreatedOwnerMail($rental, $logRow->subject));
            } elseif ($logRow->type === 'booking_cancelled_owner') {
                $reason = is_array($logRow->meta) ? ($logRow->meta['reason'] ?? null) : null;
                Mail::to($logRow->to_email)->send(new BookingCancelledOwnerMail($rental, $logRow->subject, $reason ? (string) $reason : null));
            } else {
                $logRow->update([
                    'status' => 'failed',
                    'error_message' => 'Unknown email type: ' . $logRow->type,
                ]);
                return;
            }

            $logRow->update([
                'status' => 'sent',
                'error_message' => null,
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $logRow->id,
                'type' => $logRow->type,
                'to' => $logRow->to_email,
                'rental_id' => $logRow->rental_id,
                'error' => $e->getMessage(),
            ]);

            $logRow->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

