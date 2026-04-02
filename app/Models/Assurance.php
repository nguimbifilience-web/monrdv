<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    use BelongsToClinic;

    protected $fillable = [
        'clinic_id',
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