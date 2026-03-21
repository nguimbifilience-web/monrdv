<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'specialite_id', 'email'];

    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }
}