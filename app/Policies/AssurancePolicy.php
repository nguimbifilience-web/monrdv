<?php

namespace App\Policies;

use App\Models\Assurance;
use App\Models\User;

class AssurancePolicy
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

    public function view(User $user, Assurance $assurance): bool
    {
        return $user->clinic_id === $assurance->clinic_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Assurance $assurance): bool
    {
        return $user->isAdmin() && $user->clinic_id === $assurance->clinic_id;
    }

    public function delete(User $user, Assurance $assurance): bool
    {
        return $user->isAdmin() && $user->clinic_id === $assurance->clinic_id;
    }
}
