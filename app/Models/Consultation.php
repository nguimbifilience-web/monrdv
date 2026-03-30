<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id', 'medecin_id', 'rendez_vous_id',
        'montant_total', 'taux_couverture',
        'montant_assurance', 'montant_patient',
        'montant_donne', 'montant_rendu', 'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function rendezvous()
    {
        return $this->belongsTo(RendezVous::class, 'rendez_vous_id');
    }
}
