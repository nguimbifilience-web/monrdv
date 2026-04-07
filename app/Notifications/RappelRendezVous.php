<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelRendezVous extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RendezVous $rdv) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $medecin = $this->rdv->medecin;
        $date = \Carbon\Carbon::parse($this->rdv->date_rv)->format('d/m/Y');
        $heure = $this->rdv->heure_rv ? substr($this->rdv->heure_rv, 0, 5) : 'non définie';

        return (new MailMessage)
            ->subject('Rappel : Rendez-vous demain')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Nous vous rappelons votre rendez-vous prévu demain :")
            ->line("**Date :** {$date}")
            ->line("**Heure :** {$heure}")
            ->line("**Médecin :** Dr. {$medecin->nom} {$medecin->prenom}")
            ->line("**Motif :** " . ($this->rdv->motif ?? 'Non précisé'))
            ->action('Voir mes rendez-vous', url('/espace-patient/mes-rendezvous'))
            ->line('Si vous ne pouvez pas vous présenter, veuillez annuler votre rendez-vous.')
            ->salutation('Cordialement, l\'équipe MonRDV');
    }
}
