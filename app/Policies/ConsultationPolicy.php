<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isStaff() || $user->isAdmin();
    }

    public function view(User $user, Consultation $consultation): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $consultation->clinic_id;
        }
        if ($user->isMedecin() && $user->medecin) {
            return $consultation->medecin_id === $user->medecin->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStaff() || $user->isAdmin();
    }
}
