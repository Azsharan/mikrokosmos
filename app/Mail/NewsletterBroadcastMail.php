<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Newsletter $newsletter)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.newsletters.broadcast',
            with: [
                'newsletter' => $this->newsletter,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
