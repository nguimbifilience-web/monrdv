<?php

namespace App\Policies;

use App\Models\DocumentPatient;
use App\Models\User;

class DocumentPatientPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function view(User $user, DocumentPatient $document): bool
    {
        if ($user->isStaff() || $user->isAdmin()) {
            return $user->clinic_id === $document->clinic_id;
        }
        if ($user->isPatient() && $user->patient) {
            return $document->patient_id === $user->patient->id;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isStaff() || $user->isAdmin();
    }

    public function delete(User $user, DocumentPatient $document): bool
    {
        if ($user->isAdmin()) {
            return $user->clinic_id === $document->clinic_id;
        }
        if ($user->isPatient() && $user->patient) {
            return $document->patient_id === $user->patient->id;
        }
        return false;
    }
}
