<?php

namespace App\Models\Traits;

use App\Models\Clinic;
use App\Models\Scopes\ClinicScope;

trait BelongsToClinic
{
    public static function bootBelongsToClinic(): void
    {
        // Appliquer le filtre automatique sur toutes les requêtes
        static::addGlobalScope(new ClinicScope);

        // Remplir automatiquement clinic_id lors de la création
        static::creating(function ($model) {
            if (empty($model->clinic_id)) {
                $user = auth()->user();
                if ($user && $user->clinic_id) {
                    $model->clinic_id = $user->clinic_id;
                }
            }
        });
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
}
