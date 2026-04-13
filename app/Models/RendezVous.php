<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory, BelongsToClinic;

    protected $table = 'rendez_vous';

    // On utilise exactement les noms de ton DESCRIBE MySQL
    protected $fillable = [
        'clinic_id',
        'date_rv',
        'heure_rv',
        'motif',
        'statut',
        'source',
        'patient_id',
        'medecin_id'
    ];

    protected $casts = [
        'date_rv' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class, 'rendez_vous_id');
    }
}