<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCreatedOwnerMail extends Mailable
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
            ->view('emails.bookings.owner-created-html')
            ->text('emails.bookings.owner-created-text')
            ->with([
                'rental' => $this->rental,
            ]);
    }
}
