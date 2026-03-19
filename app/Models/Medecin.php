<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;

    // Autorise Laravel à remplir ces colonnes (Mass Assignment)
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'specialite_id',
    ];

    /**
     * Relation : Un médecin appartient à une spécialité.
     * Cela permet de faire : $medecin->specialite->nom
     */
    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }
}