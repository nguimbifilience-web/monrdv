<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentPatient extends Model
{
    use BelongsToClinic;
    use SoftDeletes;

    public const CATEGORIE_INFO = 'informations';
    public const CATEGORIE_MEDICAL = 'medical';

    public const CATEGORIES = [
        self::CATEGORIE_INFO => 'Informations',
        self::CATEGORIE_MEDICAL => 'Medical',
    ];

    protected $table = 'documents_patient';

    protected $fillable = ['clinic_id', 'patient_id', 'nom', 'type', 'categorie', 'fichier'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function scopeInformations($query)
    {
        return $query->where('categorie', self::CATEGORIE_INFO);
    }

    public function scopeMedical($query)
    {
        return $query->where('categorie', self::CATEGORIE_MEDICAL);
    }
}
