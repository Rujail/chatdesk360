<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL; // Import URL facade

class WelcomeAgentNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $siteName;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $siteName = null)
    {
        $this->token = $token;
        $this->siteName = $siteName ?? config('app.name');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // ✅ Generate a secure signed URL to reset password
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Welcome to ' . $this->siteName . '!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('An account has been created for you on ' . $this->siteName . '.')
            ->line('To get started, please set your password by clicking the button below.')
            ->action('Set Your Password', $resetUrl)
            ->line('This secure link will expire in 60 minutes.')
            ->line('If you did not expect this account, please ignore this email.');
    }
}