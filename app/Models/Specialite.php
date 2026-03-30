<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialite extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description', 'icone', 'tarif_consultation'];

    /**
     * RELATION : Une spécialité possède plusieurs médecins.
     * Permet de faire : $specialite->medecins
     */
    public function medecins()
    {
        return $this->hasMany(Medecin::class);
    }

    /**
     * RELATION INDIRECTE : Accéder aux rendez-vous d'une spécialité.
     * Utile pour voir tous les RDV en "Cardiologie" par exemple.
     */
    public function rendezvous()
    {
        return $this->hasManyThrough(RendezVous::class, Medecin::class);
    }
}