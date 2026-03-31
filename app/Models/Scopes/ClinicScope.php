<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClinicScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if ($user && $user->clinic_id && !$user->isSuperAdmin()) {
            $builder->where($model->getTable() . '.clinic_id', $user->clinic_id);
        }
    }
}
