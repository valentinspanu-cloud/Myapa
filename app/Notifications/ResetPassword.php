<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Resetare parolă - MyAPA')
            ->greeting('Bună ziua!')
            ->line('Ai primit acest email deoarece am primit o cerere de resetare a parolei pentru contul tău.')
            ->action('Resetează parola', $url)
            ->line('Linkul de resetare va expira în 60 de minute.')
            ->line('Dacă nu ai solicitat resetarea parolei, ignoră acest email.')
            ->salutation('MyAPA · Aquaserv Tulcea');
    }
}
