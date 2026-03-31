<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory, BelongsToClinic;

    // Autorise l'enregistrement massif de ces colonnes
    protected $fillable = ['nom', 'prenom', 'telephone', 'specialite_id', 'tarif_heure', 'heures_mois', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Récupérer l'email du compte utilisateur lié
    public function getEmailAttribute()
    {
        return $this->user?->email;
    }

    // Relation pour récupérer le NOM de la spécialité
    public function specialite()
    {
        return $this->belongsTo(Specialite::class, 'specialite_id');
    }

    // Relation pour le planning
    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }
}