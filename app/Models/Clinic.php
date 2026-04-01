<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = ['name', 'slug', 'email', 'phone', 'address', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function medecins()
    {
        return $this->hasMany(Medecin::class);
    }

    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function specialites()
    {
        return $this->hasMany(Specialite::class);
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
