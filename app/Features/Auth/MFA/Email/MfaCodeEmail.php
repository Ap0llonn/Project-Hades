<?php

namespace App\Features\Auth\MFA\Email;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MfaCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $expiresInMinutes = 10,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.mfa-verification-code',
            with: [
                'verificationCode' => $this->code,
                'expiresInMinutes' => $this->expiresInMinutes,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
