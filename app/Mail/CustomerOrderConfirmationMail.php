<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance mailable konfirmasi pesanan pelanggan.
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Amplop subjek email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🛒 Konfirmasi Pesanan Gantungan Kunci NFC FlexiTag #{$this->order->order_no}",
        );
    }

    /**
     * Tampilan konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_order_confirmation',
        );
    }

    /**
     * Lampiran email.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
