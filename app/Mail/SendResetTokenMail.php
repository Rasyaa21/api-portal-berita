<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use Ichtrojan\Otp\Otp;

class SendResetTokenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($notifiableEmail)
    {
        $otp = new Otp();
        $this->otpCode = $otp->generate($notifiableEmail, 'numeric', 6, 15)->token;
        $this->message = 'Use the code below to reset your password.';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('admin@app.com', 'Test Sender'),
            subject: 'Password Reset Token',
        );
    }

    /**
     * Build the message content directly without a view.
     */
    public function build()
    {
        $body = "{$this->message}\n\nCode: {$this->otpCode}";
        return $this->from('test@gmail.com')
                    ->subject('Password Reset Token')
                    ->markdown('emails.plain_text')
                    ->with([
                        'message' => $body
                    ]);
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
