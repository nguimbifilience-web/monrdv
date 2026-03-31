<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PatientCreationCode extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,
        public string $patientNom,
        public string $patientPrenom
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Code de validation — Création patient')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line("Une demande de création de compte patient a été initiée pour **{$this->patientPrenom} {$this->patientNom}**.")
            ->line("Votre code de validation est :")
            ->line("## {$this->code}")
            ->line("Ce code expire dans 10 minutes.")
            ->line("Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.");
    }
}
