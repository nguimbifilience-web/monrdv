<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Super admin peut tout faire
     */
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

    public function view(User $user, Patient $patient): bool
    {
        // Staff de la même clinique ou le patient lui-même
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $patient->clinic_id;
        }
        if ($user->isPatient()) {
            return $user->id === $patient->user_id;
        }
        if ($user->isMedecin()) {
            return $user->clinic_id === $patient->clinic_id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStaff() || $user->isAdmin();
    }

    public function update(User $user, Patient $patient): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $patient->clinic_id;
        }
        if ($user->isPatient()) {
            return $user->id === $patient->user_id;
        }
        return false;
    }

    public function delete(User $user, Patient $patient): bool
    {
        return ($user->isAdmin()) && $user->clinic_id === $patient->clinic_id;
    }
}
