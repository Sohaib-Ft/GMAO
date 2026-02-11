<?php

namespace App\Mail;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EquipmentIrreparableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $workOrder;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(WorkOrder $workOrder, ?string $reason)
    {
        $this->workOrder = $workOrder;
        $this->reason = $reason ?? 'Aucune raison spécifiée';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Important : Votre équipement est déclaré irréparable',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.equipment_irreparable',
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
