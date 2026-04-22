<?php

namespace App\Notifications;

use App\Models\Organisation;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Organisation $organisation,
        public Role $role,
        public string $codeProvince
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Accès activé — AlertBook')
            ->view('emails.account-activated-html', [
                'userName' => $notifiable->name,
                'organisation' => $this->organisation->org_name,
                'role' => $this->role->name,
                'province' => $this->codeProvince,
                'loginUrl' => url('/login'),
            ]);
    }
}
