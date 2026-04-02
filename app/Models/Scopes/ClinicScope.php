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

            if ($user && !$user->isSuperAdmin()) {
                if ($user->clinic_id) {
                    $builder->where($model->getTable() . '.clinic_id', $user->clinic_id);
                } else {
                    // User sans clinique ne voit rien (sauf super admin)
                    $builder->where($model->getTable() . '.clinic_id', 0);
                }
            }
        } finally {
            self::$applying = false;
        }
    }
}
