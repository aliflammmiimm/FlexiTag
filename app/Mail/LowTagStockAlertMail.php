<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowTagStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance mailable untuk peringatan stok tag menipis ke admin.
     */
    public function __construct(
        public int $remainingCount,
        public int $activeCount,
        public int $totalCount
    ) {}

    /**
     * Dapatkan amplop (envelope) email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠️ PERINGATAN: Sisa Stok Tag Belum Diaktivasi Menipis (Tersisa {$this->remainingCount} Unit) — FlexiTag Admin",
        );
    }

    /**
     * Dapatkan definisi konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.low_tag_stock_alert',
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
