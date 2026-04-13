<?php

namespace App\Features\Auth\EmailValidation;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class VerificationLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public string $verificationUrl,
        public string $illustrationUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm your email address',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.confirmation',
            with: [
                'firstName' => $this->firstName,
                'confirmationUrl' => $this->verificationUrl,
                'confirmationImageUrl' => $this->illustrationUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
