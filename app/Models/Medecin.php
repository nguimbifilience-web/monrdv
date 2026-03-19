<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'specialite_id'];

    /**
     * RELATION : Le médecin appartient à une seule spécialité.
     */
    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }

    /**
     * RELATION : Le médecin possède plusieurs rendez-vous.
     */
    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }
}