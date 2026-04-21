<?php

namespace App\Policies;

use App\Models\DocumentPatient;
use App\Models\Patient;
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

    public function viewCategorie(User $user, Patient $patient, string $categorie): bool
    {
        if (!$this->sameClinic($user, $patient)) {
            return false;
        }

        if ($user->isPatient()) {
            return $user->patient && $user->patient->id === $patient->id;
        }

        if ($categorie === DocumentPatient::CATEGORIE_INFO) {
            return $user->isAdmin() || $user->isSecretaire() || $user->isMedecin();
        }

        return $user->isMedecin() || $user->isSecretaire();
    }

    public function uploadCategorie(User $user, Patient $patient, string $categorie): bool
    {
        if (!$this->sameClinic($user, $patient)) {
            return false;
        }

        if ($user->isPatient()) {
            return $user->patient && $user->patient->id === $patient->id;
        }

        if ($categorie === DocumentPatient::CATEGORIE_INFO) {
            return $user->isAdmin() || $user->isSecretaire() || $user->isMedecin();
        }

        return $user->isMedecin();
    }

    public function view(User $user, DocumentPatient $document): bool
    {
        return $this->viewCategorie($user, $document->patient, $document->categorie);
    }

    public function download(User $user, DocumentPatient $document): bool
    {
        return $this->view($user, $document);
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isAdmin() || $user->isSecretaire() || $user->isMedecin();
    }

    public function delete(User $user, DocumentPatient $document): bool
    {
        if (!$this->sameClinic($user, $document->patient)) {
            return false;
        }

        if ($user->isPatient()) {
            return $user->patient && $user->patient->id === $document->patient_id;
        }

        if ($document->categorie === DocumentPatient::CATEGORIE_INFO) {
            return $user->isAdmin() || $user->isSecretaire() || $user->isMedecin();
        }

        return $user->isMedecin();
    }

    private function sameClinic(User $user, Patient $patient): bool
    {
        return $user->clinic_id === $patient->clinic_id;
    }
}
