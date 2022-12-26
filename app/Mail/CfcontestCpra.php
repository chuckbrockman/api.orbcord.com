<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CfcontestCpra extends Mailable
{
    use Queueable, SerializesModels;

    public $id;

    public $referrer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $referrer)
    {
        $this->id = $id;
        $this->referrer = $referrer;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'cfcontests.com CPRA Submission',
            from: new Address('cf-developers@vardapartners.com'),
            replyTo: [ 'cf-developers@vardapartners.com' ]
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
            view: 'emails.classic-firearms.cfcontests-cpra',
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
