<?php

namespace App\Services;

use App\Mail\BookingCancelledOwnerMail;
use App\Mail\BookingCompletedRenterMail;
use App\Mail\BookingConfirmedRenterMail;
use App\Mail\BookingCreatedOwnerMail;
use App\Mail\BookingRejectedRenterMail;
use App\Mail\DispatchCreatedOwnerMail;
use App\Models\BookingEmailLog;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class BookingEmailService
{
    public function sendBookingCreatedOwner(Rental $rental): void
    {
        $vehicle = $rental->vehicle;
        $owner = $vehicle?->user;
        if (!$owner || !$owner->email) {
            return;
        }

        $subject = $this->subjectLine('New Booking', $rental);

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'booking_created_owner',
            'to_email' => $owner->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'owner_id' => $owner->id,
            ],
        ]);

        try {
            Mail::to($owner->email)->send(new BookingCreatedOwnerMail($rental, $subject));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendBookingCancelledOwner(Rental $rental, ?string $reason = null): void
    {
        $vehicle = $rental->vehicle;
        $owner = $vehicle?->user;
        if (!$owner || !$owner->email) {
            return;
        }

        $subject = $this->subjectLine('Booking Cancelled', $rental);

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'booking_cancelled_owner',
            'to_email' => $owner->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'owner_id' => $owner->id,
                'reason' => $reason,
            ],
        ]);

        try {
            Mail::to($owner->email)->send(new BookingCancelledOwnerMail($rental, $subject, $reason));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendBookingRejectedRenter(Rental $rental, string $reason): void
    {
        $renter = $rental->user;
        if (!$renter || !$renter->email) {
            return;
        }

        $subject = $this->subjectLine('Booking Rejected', $rental);

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'booking_rejected_renter',
            'to_email' => $renter->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'renter_id' => $renter->id,
                'reason' => $reason,
            ],
        ]);

        try {
            Mail::to($renter->email)->send(new BookingRejectedRenterMail($rental, $subject, $reason));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendBookingConfirmedRenter(Rental $rental): void
    {
        $renter = $rental->user;
        if (!$renter || !$renter->email) {
            return;
        }

        $subject = $this->subjectLine('Booking Confirmed', $rental);

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'booking_confirmed_renter',
            'to_email' => $renter->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'renter_id' => $renter->id,
            ],
        ]);

        try {
            Mail::to($renter->email)->send(new BookingConfirmedRenterMail($rental, $subject));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendBookingCompletedRenter(Rental $rental): void
    {
        $renter = $rental->user;
        if (!$renter || !$renter->email) {
            return;
        }

        $subject = $this->subjectLine('Travel Completed', $rental);

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'booking_completed_renter',
            'to_email' => $renter->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'renter_id' => $renter->id,
            ],
        ]);

        try {
            Mail::to($renter->email)->send(new BookingCompletedRenterMail($rental, $subject));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendDispatchCreatedOwner(Rental $rental): void
    {
        $vehicle = $rental->vehicle;
        $owner = $vehicle?->user;
        if (!$owner || !$owner->email) {
            return;
        }

        $subject = $this->subjectLine('Vehicle Dispatched', $rental);

        $byName = $rental->user?->name ?? 'System';
        $byEmail = $rental->user?->email ?? '';

        $log = BookingEmailLog::create([
            'rental_id' => $rental->id,
            'type' => 'dispatch_created_owner',
            'to_email' => $owner->email,
            'subject' => $subject,
            'status' => 'queued',
            'meta' => [
                'owner_id' => $owner->id,
                'dispatched_by_user_id' => $rental->user_id,
            ],
        ]);

        try {
            Mail::to($owner->email)->send(new DispatchCreatedOwnerMail($rental, $subject, $byName, $byEmail));
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);
        } catch (Throwable $e) {
            Log::error('booking_email_send_failed', [
                'email_log_id' => $log->id,
                'type' => $log->type,
                'to' => $log->to_email,
                'rental_id' => $log->rental_id,
                'error' => $e->getMessage(),
            ]);
            $log->update([
                'status' => 'failed',
                'attempts' => (int) $log->attempts + 1,
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    private function subjectLine(string $prefix, Rental $rental): string
    {
        $vehicle = $rental->vehicle;
        $ref = $rental->bookingReference();

        $name = $vehicle?->name ?? 'Vehicle';
        $color = $vehicle?->color ? (' - ' . $vehicle->color) : '';
        $year = $vehicle?->year_model ? (' - ' . $vehicle->year_model) : '';

        $from = $rental->datetime_from ? $rental->datetime_from->format('M d') : '';
        $to = $rental->datetime_to ? $rental->datetime_to->format('M d, Y') : '';
        $range = trim($from . ($to ? ('-' . $to) : ''));

        $parts = [
            $prefix . ': #' . $ref,
            $name . $color . $year,
        ];
        if ($range) {
            $parts[] = $range;
        }

        return implode(' - ', $parts);
    }
}
