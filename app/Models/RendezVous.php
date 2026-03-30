<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    // On utilise exactement les noms de ton DESCRIBE MySQL
    protected $fillable = [
        'date_rv', 
        'heure_rv', 
        'motif', 
        'statut', 
        'patient_id', 
        'medecin_id'
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