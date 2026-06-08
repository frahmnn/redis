<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyForRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $assistant;
    public $formattedDate;
    public $formattedCreatedAt;
    public $requirements;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation, $assistant, $formattedDate, $formattedCreatedAt, $requirements)
    {
        $this->reservation = $reservation;
        $this->assistant = $assistant;
        $this->formattedDate = $formattedDate;
        $this->formattedCreatedAt = $formattedCreatedAt;
        $this->requirements = $requirements;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Permintaan Pendampingan Baru',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notify-for-request',
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
