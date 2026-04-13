<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'price_monthly', 'max_medecins', 'max_rdv_monthly',
        'includes_insurance', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'includes_insurance' => 'boolean',
            'price_monthly' => 'integer',
            'max_medecins' => 'integer',
            'max_rdv_monthly' => 'integer',
        ];
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_monthly, 0, ',', ' ') . ' XAF';
    }
}
