<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IndexReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $sold;

    public function __construct($user, $sold = 0)
    {
        $this->user = $user;
        $this->sold = $sold;
    }

    public function build()
    {
        return $this->subject('Aquaserv Tulcea - Transmitere index consum apa luna ' . now()->locale('ro')->isoFormat('MMMM YYYY'))
                    ->view('emails.index_reminder')
                    ->with([
                        'user' => $this->user,
                        'sold' => $this->sold,
                    ]);
    }
}
