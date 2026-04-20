<?php

namespace App\Policies;

use App\Models\FeuilleExamen;
use App\Models\User;

class FeuilleExamenPolicy
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

    public function view(User $user, FeuilleExamen $examen): bool
    {
        if ($user->clinic_id !== $examen->clinic_id) {
            return false;
        }
        if ($user->isMedecin() && $user->medecin) {
            return $examen->medecin_id === $user->medecin->id;
        }
        if ($user->isPatient() && $user->patient) {
            return $examen->patient_id === $user->patient->id;
        }
        return $user->isStaff() || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isMedecin();
    }

    public function update(User $user, FeuilleExamen $examen): bool
    {
        return $user->isMedecin()
            && $user->medecin
            && $examen->medecin_id === $user->medecin->id;
    }

    public function delete(User $user, FeuilleExamen $examen): bool
    {
        return $user->isMedecin()
            && $user->medecin
            && $examen->medecin_id === $user->medecin->id;
    }
}
