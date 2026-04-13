<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalAssurance extends Model
{
    protected $table = 'global_assurances';

    protected $fillable = ['nom', 'icone', 'type', 'pays', 'contact', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
