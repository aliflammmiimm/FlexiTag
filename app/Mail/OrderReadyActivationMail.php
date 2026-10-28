<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReadyActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance mailable untuk pengiriman kode aktivasi pesanan siap.
     *
     * @param  Collection  $tags
     */
    public function __construct(
        public Order $order,
        public $tags
    ) {}

    /**
     * Dapatkan amplop (envelope) email.
     */
    public function envelope(): Envelope
    {
        $orderNo = $this->order->order_no;

        return new Envelope(
            subject: "🎉 Pembayaran Dikonfirmasi! Kode Aktivasi Gantungan Kunci NFC FlexiTag #{$orderNo}",
        );
    }

    /**
     * Dapatkan definisi konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order_ready_activation',
        );
    }

    /**
     * Dapatkan lampiran email.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
