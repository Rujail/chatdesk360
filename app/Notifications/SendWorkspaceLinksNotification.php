<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class SendWorkspaceLinksNotification extends Notification
{
    use Queueable;

    public Collection $tenants;

    public function __construct(Collection $tenants)
    {
        $this->tenants = $tenants;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your ChatDesk Workspaces')
            ->greeting('Hello!')
            ->line('You requested your workspace links. Click on a workspace below to log in:');

        foreach ($this->tenants as $tenant) {
            $url = (request()->secure() ? 'https' : 'http') . '://' . $tenant->domain_name . '/login';
            $mail->action('Login to ' . $tenant->domain_name, $url);
        }

        $mail->line('If you did not request this, no further action is required.');

        return $mail;
    }
}