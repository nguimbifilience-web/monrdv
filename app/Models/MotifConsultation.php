<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class MotifConsultation extends Model
{
    use BelongsToClinic;

    protected $fillable = ['clinic_id', 'specialite_id', 'libelle'];

    public function specialite()
    {
        return $this->belongsTo(Specialite::class);
    }
}
