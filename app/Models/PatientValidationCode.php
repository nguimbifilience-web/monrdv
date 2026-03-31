<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;

class PatientValidationCode extends Model
{
    use BelongsToClinic;

    protected $fillable = ['code', 'patient_nom', 'patient_prenom', 'requested_by', 'used', 'expires_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
        ];
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }

    public function scopePending($query)
    {
        return $query->where('used', false)->where('expires_at', '>', now());
    }
}
