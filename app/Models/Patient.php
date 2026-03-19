<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    // Champs autorisés à être remplis
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'assurance_id',
        'numero_assurance',
    ];

    /**
     * Relation : Un patient peut appartenir à une assurance
     */
    public function assurance()
    {
        return $this->belongsTo(Assurance::class);
    }
}