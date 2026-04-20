<?php

namespace App\Policies;

use App\Models\Specialite;
use App\Models\User;

class SpecialitePolicy
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
        return $user->clinic_id !== null;
    }

    public function view(User $user, Specialite $specialite): bool
    {
        return $user->clinic_id === $specialite->clinic_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Specialite $specialite): bool
    {
        return $user->isAdmin() && $user->clinic_id === $specialite->clinic_id;
    }

    public function delete(User $user, Specialite $specialite): bool
    {
        return $user->isAdmin() && $user->clinic_id === $specialite->clinic_id;
    }
}
