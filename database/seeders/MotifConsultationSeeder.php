<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Specialite;
use App\Models\MotifConsultation;
use Illuminate\Database\Seeder;

class MotifConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $motifs = [
            'Cardiologie' => [
                'Douleur thoracique',
                'Palpitations',
                'Hypertension artérielle',
                'Essoufflement',
                'Suivi post-opératoire cardiaque',
                'Bilan cardiaque',
                'Insuffisance cardiaque',
                'Contrôle tension',
                'ECG de contrôle',
                'Consultation de suivi',
            ],
            'Pédiatrie' => [
                'Vaccination',
                'Fièvre',
                'Toux / Rhume',
                'Diarrhée / Vomissements',
                'Éruption cutanée',
                'Suivi de croissance',
                'Bilan de santé',
                'Otite',
                'Douleur abdominale',
                'Consultation de suivi',
            ],
            'Généraliste' => [
                'Consultation générale',
                'Fièvre',
                'Douleur abdominale',
                'Maux de tête',
                'Fatigue chronique',
                'Bilan de santé',
                'Certificat médical',
                'Renouvellement ordonnance',
                'Douleur articulaire',
                'Infection urinaire',
                'Toux / Rhume',
                'Consultation de suivi',
            ],
            'Dentiste' => [
                'Douleur dentaire',
                'Carie',
                'Détartrage',
                'Extraction dentaire',
                'Prothèse dentaire',
                'Gingivite',
                'Contrôle dentaire',
                'Blanchiment',
                'Appareil orthodontique',
                'Consultation de suivi',
            ],
            'Ophtalmologie' => [
                'Baisse de vision',
                'Douleur oculaire',
                'Rougeur des yeux',
                'Contrôle de la vue',
                'Prescription lunettes',
                'Glaucome',
                'Cataracte',
                'Sécheresse oculaire',
                'Corps étranger',
                'Consultation de suivi',
            ],
        ];

        $clinics = Clinic::all();

        foreach ($clinics as $clinic) {
            foreach ($motifs as $specialiteNom => $libellesMotifs) {
                $specialite = Specialite::withoutGlobalScopes()
                    ->where('clinic_id', $clinic->id)
                    ->where('nom', $specialiteNom)
                    ->first();

                if (!$specialite) {
                    continue;
                }

                foreach ($libellesMotifs as $libelle) {
                    MotifConsultation::withoutGlobalScopes()->firstOrCreate([
                        'clinic_id' => $clinic->id,
                        'specialite_id' => $specialite->id,
                        'libelle' => $libelle,
                    ]);
                }
            }
        }
    }
}
