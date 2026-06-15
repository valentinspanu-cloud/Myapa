<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;
use Config;
use URL;

class VerifyEmail extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        $temporarySignedURL = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
        );
        return $temporarySignedURL;
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('Verificare adresa de email')
            ->line('Apasa butonul de mai jos pentru a-ti verifica adresa de email.')
            ->action('Verifica Email', $verificationUrl)
            ->line('Daca nu ai creat un cont, ignora acest mesaj.');
    }
}
