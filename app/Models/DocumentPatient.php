<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class DocumentPatient extends Model
{
    use BelongsToClinic;

    protected $table = 'documents_patient';

    protected $fillable = ['clinic_id', 'patient_id', 'nom', 'type', 'fichier'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
