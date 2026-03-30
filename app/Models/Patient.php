<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Patient extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'quartier',
        'est_assure', 'assurance_id', 'medecin_id',
        'notes_medicales', 'observations',
    ];

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', '%'.$search.'%')
                  ->orWhere('prenom', 'like', '%'.$search.'%')
                  ->orWhere('telephone', 'like', '%'.$search.'%');
            });
        })->when($filters['medecin_id'] ?? null, function ($query, $medecinId) {
            $query->where('medecin_id', $medecinId);
        })->when(isset($filters['est_assure']) && $filters['est_assure'] !== '', function ($query) use ($filters) {
            $query->where('est_assure', $filters['est_assure']);
        })->when($filters['assurance_id'] ?? null, function ($query, $assuranceId) {
            $query->where('assurance_id', $assuranceId);
        });
    }

    public function assurance() {
        return $this->belongsTo(Assurance::class);
    }

    public function medecin() {
        return $this->belongsTo(Medecin::class);
    }

    public function rendezvous() {
        return $this->hasMany(RendezVous::class, 'patient_id');
    }
}
