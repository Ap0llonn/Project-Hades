<?php

namespace App\Features\Dashboard\Service\Share\Create;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class PasswordShareNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $ownerEmail,
        public string $serviceName,
        public string $dashboardUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A vault item was shared with you',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password-share-notification',
            with: [
                'ownerEmail' => $this->ownerEmail,
                'serviceName' => $this->serviceName,
                'dashboardUrl' => $this->dashboardUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
