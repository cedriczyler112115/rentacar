<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedRenterMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Rental $rental,
        public string $subjectLine,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.bookings.renter-confirmed-html')
            ->text('emails.bookings.renter-confirmed-text')
            ->with([
                'rental' => $this->rental,
            ]);
    }
}

