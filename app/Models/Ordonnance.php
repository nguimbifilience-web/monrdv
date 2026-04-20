<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ordonnance extends Model
{
    use BelongsToClinic;
    use SoftDeletes;

    protected $fillable = [
        'clinic_id', 'medecin_id', 'patient_id', 'consultation_id',
        'date', 'notes_generales',
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
        return $this->hasMany(OrdonnanceLigne::class);
    }
}
