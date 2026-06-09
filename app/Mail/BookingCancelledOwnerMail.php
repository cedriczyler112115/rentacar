<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledOwnerMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Rental $rental,
        public string $subjectLine,
        public ?string $reason,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.bookings.owner-cancelled-html')
            ->text('emails.bookings.owner-cancelled-text')
            ->with([
                'rental' => $this->rental,
                'reason' => $this->reason,
            ]);
    }
}
