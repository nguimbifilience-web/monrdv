<?php

namespace App\Console\Commands;

use App\Models\RendezVous;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateRdvStatuts extends Command
{
    protected $signature = "rdv:update-statuts";
    protected $description = "Met à jour les statuts des RDV dépassés";

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();
        $heureActuelle = $now->format('H:i:s');

        // RDV dépassés aujourd'hui (heure passée) ou jours précédents
        $rdvDepasses = RendezVous::where('statut', 'en_attente')
            ->where(function ($q) use ($today, $heureActuelle) {
                $q->where('date_rv', '<', $today)
                   ->orWhere(function ($q2) use ($today, $heureActuelle) {
                       $q2->where('date_rv', $today)
                           ->where('heure_rv', '<', $heureActuelle);
                   });
            })
            ->get();

        $termines = 0;
        $annules = 0;

        foreach ($rdvDepasses as $rdv) {
            if ($rdv->consultation()->exists()) {
                $rdv->update(['statut' => 'termine']);
                $termines++;
            } else {
                $rdv->update(['statut' => 'annule']);
                $annules++;
            }
        }

        $this->info("Terminés: {$termines} | Annulés: {$annules}");
    }
}
