<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $emailBody;
    public array $emailAttachments;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body, array $attachments = [])
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->emailAttachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->emailBody,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->emailAttachments as $filePath) {
            if (file_exists(storage_path('app/' . $filePath))) {
                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath(storage_path('app/' . $filePath));
            }
        }

        return $attachments;
    }
}
