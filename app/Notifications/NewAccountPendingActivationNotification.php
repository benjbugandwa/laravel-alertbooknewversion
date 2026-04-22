<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAccountPendingActivationNotification extends Notification
{
    use Queueable;

    public function __construct(public User $newUser) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau compte en attente d’activation — AlertBook')
            ->view('emails.new-account-pending-activation-html', [
                'adminName' => $notifiable->name,
                'newUser'   => $this->newUser,
                'manageUrl' => url('/users'),
            ]);
    }
}
