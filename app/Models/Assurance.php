<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    protected $fillable = [
        'nom',
        'type',
        'nom_referent',
        'telephone',
        'email',
        'taux_couverture',
    ];

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}