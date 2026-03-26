<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    // TRÈS IMPORTANT : Sans cette ligne, l'enregistrement échoue silencieusement
    protected $fillable = ['medecin_id', 'date_travail'];

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }
}