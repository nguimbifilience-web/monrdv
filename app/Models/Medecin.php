<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;

    // Autorise l'enregistrement massif de ces colonnes
    protected $fillable = ['nom', 'prenom', 'telephone', 'specialite_id'];

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
}