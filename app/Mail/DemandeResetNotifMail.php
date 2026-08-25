<?php

namespace App\Mail;

use App\Models\User;
use App\Models\DemandeResetMdp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeResetNotifMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User            $userDemandeur,
        public DemandeResetMdp $demande
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Demande de réinitialisation mot de passe — Dima Groupe',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demande-reset-notif',
        );
    }
}
