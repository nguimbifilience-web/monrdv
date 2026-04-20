<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentPatient extends Model
{
    use BelongsToClinic;
    use SoftDeletes;

    protected $table = 'documents_patient';

    protected $fillable = ['clinic_id', 'patient_id', 'nom', 'type', 'fichier'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
