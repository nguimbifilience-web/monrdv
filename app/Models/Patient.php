<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'date_naissance'];

    // Accesseur pour afficher "NOM Prénom"
    public function getNomCompletAttribute()
    {
        return strtoupper($this->nom) . ' ' . ucfirst($this->prenom);
    }

    // Relation : Un patient a plusieurs rendez-vous
    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }
}