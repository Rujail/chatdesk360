<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUserNotification extends Notification
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/invite/accept/' . $this->token);

        return (new MailMessage)
            ->subject('You are invited to join 360')
            ->greeting('Hello!')
            ->line('You have been invited to join our system.')
            ->action('Accept Invitation', $url)
            ->line('This link will expire soon.')
            ->line('If you did not expect this, ignore this email.');
    }
}
