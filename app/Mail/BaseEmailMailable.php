<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Traits\DS_SafeTemplateRenderer;

class BaseEmailMailable extends Mailable
{
    use Queueable, SerializesModels, DS_SafeTemplateRenderer;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected EmailTemplate $template,
        protected array $data = []
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->safeRender($this->template->subject, $this->data),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->safeRender($this->template->content, $this->data),
        );
    }
}
