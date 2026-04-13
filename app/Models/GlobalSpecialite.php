<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSpecialite extends Model
{
    protected $table = 'global_specialites';

    protected $fillable = ['nom', 'icone', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
