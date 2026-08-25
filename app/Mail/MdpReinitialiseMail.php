<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MdpReinitialiseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $nouveauMdp,
        public string $lien
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔑 Réinitialisation de votre mot de passe — Dima Groupe',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mdp-reinitialise',
        );
    }
}
