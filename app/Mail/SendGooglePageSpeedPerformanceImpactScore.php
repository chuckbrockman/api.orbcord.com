<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendGooglePageSpeedPerformanceImpactScore extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * PageSpeedAudt model
     *
     * @var \App\Models\PageSpeedAudit $pageSpeedAudit
     */
    public $pageSpeedAudit;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pageSpeedAudit)
    {
        $this->pageSpeedAudit = $pageSpeedAudit;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $subject = '🚦 Perfomance Impact Score';
        if ( isset($this->pageSpeedAudit->meta_data['url']) ) {
            $subject .= ' - ' . rtrim(preg_replace('#^https?://#', '', $this->pageSpeedAudit->meta_data['url']), '/');
        }

        Log::info('Email subject: ' . $subject);

        return new Envelope(
            subject: $subject,
            from: new Address('hi@chuckbrockman.com', 'Chuck Brockman'),
            replyTo: ['hi@chuckbrockman.com']
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.google-lighthouse.performance-impact-score',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
