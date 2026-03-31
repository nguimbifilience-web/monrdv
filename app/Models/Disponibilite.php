<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    use BelongsToClinic;

    // TRÈS IMPORTANT : Sans cette ligne, l'enregistrement échoue silencieusement
    protected $fillable = ['medecin_id', 'date_travail'];

    protected $casts = [
        'date_travail' => 'date',
    ];

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }
}