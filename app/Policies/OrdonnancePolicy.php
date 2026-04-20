<?php

namespace App\Policies;

use App\Models\Ordonnance;
use App\Models\User;

class OrdonnancePolicy
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
        return $user->isMedecin() || $user->isPatient() || $user->isStaff() || $user->isAdmin();
    }

    public function view(User $user, Ordonnance $ordonnance): bool
    {
        if ($user->clinic_id !== $ordonnance->clinic_id) {
            return false;
        }
        if ($user->isMedecin() && $user->medecin) {
            return $ordonnance->medecin_id === $user->medecin->id;
        }
        if ($user->isPatient() && $user->patient) {
            return $ordonnance->patient_id === $user->patient->id;
        }
        return $user->isStaff() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isMedecin();
    }

    public function update(User $user, Ordonnance $ordonnance): bool
    {
        return $user->isMedecin()
            && $user->medecin
            && $ordonnance->medecin_id === $user->medecin->id;
    }

    public function delete(User $user, Ordonnance $ordonnance): bool
    {
        return $user->isMedecin()
            && $user->medecin
            && $ordonnance->medecin_id === $user->medecin->id;
    }
}
