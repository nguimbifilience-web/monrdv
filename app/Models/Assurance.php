<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assurance extends Model
{
    use HasFactory;

    // On autorise Laravel à remplir ces champs automatiquement
    protected $fillable = [
        'nom',
        'taux_prise_en_charge',
        'est_partenaire',
    ];
}