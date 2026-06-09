<?php

namespace App\Mail;

use App\Models\Rental;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DispatchCreatedOwnerMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Rental $rental,
        public string $subjectLine,
        public string $dispatchedByName,
        public string $dispatchedByEmail,
    ) {
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.dispatching.owner-dispatched-html')
            ->text('emails.dispatching.owner-dispatched-text')
            ->with([
                'rental' => $this->rental,
                'dispatchedByName' => $this->dispatchedByName,
                'dispatchedByEmail' => $this->dispatchedByEmail,
            ]);
    }
}

