<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\DocumentPatient;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinicA;
    private Clinic $clinicB;
    private User $superAdmin;
    private User $adminA;
    private User $secretaireA;
    private User $medecinUserA;
    private User $patientUserA;
    private User $adminB;
    private Medecin $medecinA;
    private Patient $patientA;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinicA = Clinic::create(['name' => 'A', 'slug' => 'a', 'is_active' => true]);
        $this->clinicB = Clinic::create(['name' => 'B', 'slug' => 'b', 'is_active' => true]);

        $this->superAdmin = User::factory()->create(['role' => 'super_admin', 'clinic_id' => null]);
        $this->adminA     = User::factory()->create(['role' => 'admin',       'clinic_id' => $this->clinicA->id]);
        $this->adminB     = User::factory()->create(['role' => 'admin',       'clinic_id' => $this->clinicB->id]);
        $this->secretaireA = User::factory()->create(['role' => 'secretaire', 'clinic_id' => $this->clinicA->id]);
        $this->medecinUserA = User::factory()->create(['role' => 'medecin',   'clinic_id' => $this->clinicA->id]);
        $this->patientUserA = User::factory()->create(['role' => 'patient',   'clinic_id' => $this->clinicA->id]);

        $specialite = Specialite::create(['nom' => 'Gene', 'tarif_consultation' => 5000, 'clinic_id' => $this->clinicA->id]);
        $this->medecinA = Medecin::create([
            'nom' => 'Doc', 'prenom' => 'A', 'telephone' => '0',
            'specialite_id' => $specialite->id, 'tarif_heure' => 1, 'heures_mois' => 1,
            'clinic_id' => $this->clinicA->id, 'user_id' => $this->medecinUserA->id,
        ]);
        $this->patientA = Patient::create([
            'nom' => 'Pat', 'prenom' => 'A', 'telephone' => '1',
            'est_assure' => false, 'clinic_id' => $this->clinicA->id,
            'user_id' => $this->patientUserA->id,
        ]);
    }

    public function test_patient_policy_isolation_entre_cliniques(): void
    {
        $this->assertTrue($this->adminA->can('view', $this->patientA));
        $this->assertFalse($this->adminB->can('view', $this->patientA));
        $this->assertTrue($this->secretaireA->can('view', $this->patientA));
        $this->assertTrue($this->medecinUserA->can('view', $this->patientA));
    }

    public function test_patient_policy_patient_voit_son_dossier_pas_celui_des_autres(): void
    {
        $this->assertTrue($this->patientUserA->can('view', $this->patientA));

        $autrePatient = Patient::create([
            'nom' => 'Autre', 'prenom' => 'B', 'telephone' => '2',
            'est_assure' => false, 'clinic_id' => $this->clinicA->id,
        ]);
        $this->assertFalse($this->patientUserA->can('view', $autrePatient));
    }

    public function test_patient_policy_seul_admin_peut_supprimer(): void
    {
        $this->assertTrue($this->adminA->can('delete', $this->patientA));
        $this->assertFalse($this->secretaireA->can('delete', $this->patientA));
        $this->assertFalse($this->medecinUserA->can('delete', $this->patientA));
        $this->assertFalse($this->patientUserA->can('delete', $this->patientA));
    }

    public function test_super_admin_bypass_toutes_policies(): void
    {
        $this->assertTrue($this->superAdmin->can('view', $this->patientA));
        $this->assertTrue($this->superAdmin->can('delete', $this->patientA));
    }

    public function test_rendezvous_policy(): void
    {
        $rdv = RendezVous::create([
            'patient_id' => $this->patientA->id,
            'medecin_id' => $this->medecinA->id,
            'date_rv' => now()->addDay()->toDateString(),
            'heure_rv' => '10:00',
            'statut' => 'en_attente',
            'clinic_id' => $this->clinicA->id,
        ]);

        $this->assertTrue($this->adminA->can('view', $rdv));
        $this->assertFalse($this->adminB->can('view', $rdv));
        $this->assertTrue($this->medecinUserA->can('view', $rdv));
        $this->assertTrue($this->patientUserA->can('view', $rdv));

        // seul admin supprime
        $this->assertTrue($this->adminA->can('delete', $rdv));
        $this->assertFalse($this->secretaireA->can('delete', $rdv));

        // patient annule son RDV en_attente uniquement
        $this->assertTrue($this->patientUserA->can('cancel', $rdv));
        $rdv->update(['statut' => 'confirme']);
        $this->assertFalse($this->patientUserA->can('cancel', $rdv));
    }

    public function test_consultation_policy(): void
    {
        $consultation = Consultation::create([
            'patient_id' => $this->patientA->id,
            'medecin_id' => $this->medecinA->id,
            'montant_total' => 5000, 'taux_couverture' => 0,
            'montant_assurance' => 0, 'montant_patient' => 5000,
            'montant_donne' => 5000, 'montant_rendu' => 0,
            'clinic_id' => $this->clinicA->id,
        ]);

        $this->assertTrue($this->adminA->can('view', $consultation));
        $this->assertTrue($this->medecinUserA->can('view', $consultation));
        $this->assertFalse($this->adminB->can('view', $consultation));
        $this->assertFalse($this->patientUserA->can('view', $consultation));
    }

    public function test_document_patient_policy(): void
    {
        $doc = DocumentPatient::create([
            'patient_id' => $this->patientA->id,
            'clinic_id' => $this->clinicA->id,
            'nom' => 'test.pdf',
            'type' => 'ordonnance',
            'fichier' => 'documents/test.pdf',
        ]);

        $this->assertTrue($this->adminA->can('view', $doc));
        $this->assertTrue($this->patientUserA->can('view', $doc));
        $this->assertFalse($this->adminB->can('view', $doc));

        $this->assertTrue($this->adminA->can('delete', $doc));
        $this->assertFalse($this->secretaireA->can('delete', $doc));
        $this->assertTrue($this->patientUserA->can('delete', $doc));
    }
}
