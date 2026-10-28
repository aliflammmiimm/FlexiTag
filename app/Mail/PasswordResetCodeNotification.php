<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Buat instance notifikasi email baru dengan kode verifikasi 6 digit.
     */
    public function __construct(
        public User $user,
        public string $code
    ) {}

    /**
     * Dapatkan amplop (envelope) email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Kode Verifikasi Pemulihan Kata Sandi: '.$this->code.' — FlexiTag',
        );
    }

    /**
     * Dapatkan definisi konten email.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset_code',
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
