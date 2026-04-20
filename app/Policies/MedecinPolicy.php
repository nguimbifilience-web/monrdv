<?php

namespace App\Policies;

use App\Models\Medecin;
use App\Models\User;

class MedecinPolicy
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
        return $user->isStaff() || $user->isAdmin() || $user->isMedecin();
    }

    public function view(User $user, Medecin $medecin): bool
    {
        return $user->clinic_id === $medecin->clinic_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Medecin $medecin): bool
    {
        return $user->isAdmin() && $user->clinic_id === $medecin->clinic_id;
    }

    public function delete(User $user, Medecin $medecin): bool
    {
        return $user->isAdmin() && $user->clinic_id === $medecin->clinic_id;
    }
}
