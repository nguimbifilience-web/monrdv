<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeuilleExamenLigne extends Model
{
    protected $fillable = [
        'feuille_examen_id', 'type_examen', 'libelle', 'urgence',
    ];

    protected $casts = [
        'urgence' => 'boolean',
    ];

    public function feuilleExamen()
    {
        return $this->belongsTo(FeuilleExamen::class);
    }
}
