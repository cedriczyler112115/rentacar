<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingRejectedRenterMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Rental $rental,
        public string $subjectLine,
        public string $reason,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.bookings.renter-rejected-html')
            ->text('emails.bookings.renter-rejected-text')
            ->with([
                'rental' => $this->rental,
                'reason' => $this->reason,
            ]);
    }
}

