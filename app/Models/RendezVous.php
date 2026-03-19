<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    // On définit le nom de la table
    protected $table = 'rendez_vous';

    protected $fillable = [
        'date_rdv',
        'motif',
        'statut',
        'patient_id',
        'medecin_id',
    ];

    /**
     * Relation : Un rendez-vous appartient à un patient
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relation : Un rendez-vous appartient à un médecin
     */
    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }
} // <--- Vérifie bien qu'il n'y a qu'UNE SEULE accolade ici à la fin