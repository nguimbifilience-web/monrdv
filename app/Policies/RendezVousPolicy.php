<?php

namespace App\Policies;

use App\Models\RendezVous;
use App\Models\User;

class RendezVousPolicy
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
        return $user->isStaff() || $user->isAdmin() || $user->isMedecin() || $user->isPatient();
    }

    public function view(User $user, RendezVous $rdv): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $rdv->clinic_id;
        }
        if ($user->isMedecin() && $user->medecin) {
            return $rdv->medecin_id === $user->medecin->id;
        }
        if ($user->isPatient() && $user->patient) {
            return $rdv->patient_id === $user->patient->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isStaff() || $user->isAdmin() || $user->isPatient();
    }

    public function update(User $user, RendezVous $rdv): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $rdv->clinic_id;
        }
        return false;
    }

    public function delete(User $user, RendezVous $rdv): bool
    {
        if ($user->isAdmin()) {
            return $user->clinic_id === $rdv->clinic_id;
        }
        return false;
    }

    public function cancel(User $user, RendezVous $rdv): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $rdv->clinic_id;
        }
        if ($user->isPatient() && $user->patient) {
            return $rdv->patient_id === $user->patient->id && $rdv->statut === 'en_attente';
        }
        return false;
    }
}
