<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClinicScope implements Scope
{
    private static bool $applying = false;

    public function apply(Builder $builder, Model $model): void
    {
        if (self::$applying) {
            return;
        }

        self::$applying = true;

        try {
            $user = auth()->user();

            if ($user && $user->clinic_id && !$user->isSuperAdmin()) {
                $builder->where($model->getTable() . '.clinic_id', $user->clinic_id);
            }
        } finally {
            self::$applying = false;
        }
    }
}
