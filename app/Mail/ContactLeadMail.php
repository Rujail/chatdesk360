<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactLeadMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $leadData;

    public function __construct(array $leadData)
    {
        $this->leadData = $leadData;
    }

    public function envelope()
    {
        // Using ?? to fallback to empty string if key doesn't exist
        $firstName = $this->leadData['first_name'] ?? '';
        $lastName = $this->leadData['last_name'] ?? '';

        return new Envelope(
            subject: 'New Contact Lead Received: ' . $firstName . ' ' . $lastName,
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.contact-email', // 🔹 Changed 'view' to 'markdown'
        );
    }
}