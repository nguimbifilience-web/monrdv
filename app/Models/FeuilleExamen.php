<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class FeuilleExamen extends Model
{
    use BelongsToClinic;

    protected $table = 'feuilles_examen';

    protected $fillable = [
        'clinic_id', 'medecin_id', 'patient_id', 'consultation_id',
        'date', 'motif_clinique',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function lignes()
    {
        return $this->hasMany(FeuilleExamenLigne::class);
    }
}
