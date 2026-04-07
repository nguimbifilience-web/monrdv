<?php

namespace App\Console\Commands;

use App\Models\RendezVous;
use App\Notifications\RappelRendezVous;
use Illuminate\Console\Command;

class SendRdvReminders extends Command
{
    protected $signature = 'rdv:send-reminders';
    protected $description = 'Envoyer des rappels email pour les RDV de demain';

    public function handle(): int
    {
        $demain = now()->addDay()->toDateString();

        $rdvsDemain = RendezVous::with(['patient.user', 'medecin'])
            ->where('date_rv', $demain)
            ->whereIn('statut', ['confirme', 'en_attente'])
            ->get();

        $sent = 0;
        foreach ($rdvsDemain as $rdv) {
            if ($rdv->patient && $rdv->patient->user && $rdv->patient->user->email) {
                $rdv->patient->user->notify(new RappelRendezVous($rdv));
                $sent++;
            }
        }

        $this->info("{$sent} rappel(s) envoyé(s) pour les RDV du {$demain}.");

        return Command::SUCCESS;
    }
}
