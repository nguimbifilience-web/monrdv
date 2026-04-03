<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Clinic extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address',
        'logo_path', 'primary_color', 'secondary_color', 'sidebar_text_color',
        'is_active', 'is_blocked', 'blocked_reason', 'blocked_at', 'subscription_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
            'subscription_expires_at' => 'date',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::url($this->logo_path) : null;
    }

    public function getPrimaryColorOrDefault(): string
    {
        return $this->primary_color ?? '#1e3a8a';
    }

    public function getSecondaryColorOrDefault(): string
    {
        return $this->secondary_color ?? '#f97316';
    }

    public function getSidebarTextColorOrDefault(): string
    {
        return $this->sidebar_text_color ?? '#ffffff';
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function medecins()
    {
        return $this->hasMany(Medecin::class);
    }

    public function rendezvous()
    {
        return $this->hasMany(RendezVous::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function specialites()
    {
        return $this->hasMany(Specialite::class);
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
