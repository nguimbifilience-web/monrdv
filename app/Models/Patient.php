<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Patient extends Model
{
    protected $fillable = ['nom', 'prenom', 'telephone', 'email', 'quartier', 'est_assure', 'assurance_id', 'medecin_id'];

    // Cette fonction permet de filtrer facilement dans le Controller
    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('nom', 'like', '%'.$search.'%')
                  ->orWhere('prenom', 'like', '%'.$search.'%');
        })->when($filters['medecin_id'] ?? null, function ($query, $medecinId) {
            $query->where('medecin_id', $medecinId);
        })->when($filters['est_assure'] ?? null, function ($query, $assure) {
            $query->where('est_assure', $assure);
        });
    }

    public function assurance() {
        return $this->belongsTo(Assurance::class);
    }

    public function medecin() {
        return $this->belongsTo(Medecin::class);
    }
    public function rendezvous()
{
    return $this->hasMany(RendezVous::class, 'patient_id');
}
}