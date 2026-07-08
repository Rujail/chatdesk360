<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Package;
use App\Models\Site;

class SubscriptionPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $package;
    public $site;
    public $billingCycle;
    public $quantity;
    public $invoice;
    public $dashboardUrl; // 👈 Added this property

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, ?Package $package, Site $site, string $billingCycle, int $quantity, string $dashboardUrl, $invoice = null) // 👈 Added string $dashboardUrl
    {
        $this->user = $user;
        $this->package = $package;
        $this->site = $site;
        $this->billingCycle = $billingCycle;
        $this->quantity = $quantity;
        $this->dashboardUrl = $dashboardUrl; // 👈 Assign it here
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Subscription Purchase Confirmation - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscription-purchased',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}