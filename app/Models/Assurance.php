<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    protected $fillable = [
        'nom',              // Nom de l'assurance
        'nom_referent',     // Nom du contact principal
        'telephone',        // Contact téléphonique
        'email',            // Contact mail
        'taux_couverture'   // Pourcentage (ex: 80)
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}