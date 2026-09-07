<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierOrderDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $orderData;
    public string $emailSubject;
    public ?string $customMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(array $orderData, string $emailSubject, ?string $customMessage = null)
    {
        $this->orderData = $orderData;
        $this->emailSubject = $emailSubject;
        $this->customMessage = $customMessage;
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
            view: 'emails.supplier_order_details',
            with: [
                'orderData' => $this->orderData,
                'customMessage' => $this->customMessage,
            ],
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
