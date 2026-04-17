<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdonnanceLigne extends Model
{
    protected $fillable = [
        'ordonnance_id', 'medicament', 'posologie', 'duree', 'quantite',
    ];

    public function ordonnance()
    {
        return $this->belongsTo(Ordonnance::class);
    }
}
