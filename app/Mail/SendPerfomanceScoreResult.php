<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPerfomanceScoreResult extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Undocumented variable
     *
     * @var \App\Models\PageSpeedAudit $pageSpeedAudit
     */
    public $pageSpeedAudit;

    public $webhookData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pageSpeedAudit, $webhookData)
    {
        $this->pageSpeedAudit = $pageSpeedAudit;
        $this->webhookData = $webhookData;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {

        $subject = '🚦 Perfomance Impact Score';
        if ( isset($this->webhookData['url']) ) {
            $subject .= ' - ' . rtrim(preg_replace('#^https?://#', '', $this->webhookData['url']), '/');
        }

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
            view: 'emails.performance-score.send-score',
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
