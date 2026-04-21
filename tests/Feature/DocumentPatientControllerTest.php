<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\DocumentPatient;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Specialite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentPatientControllerTest extends TestCase
{
    use RefreshDatabase;

    private Clinic $clinic;
    private Patient $patient;
    private User $admin;
    private User $secretaire;
    private User $medecin;
    private User $patientUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->clinic = Clinic::create([
            'name' => 'Test',
            'slug' => 'test',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create(['role' => 'admin', 'clinic_id' => $this->clinic->id]);
        $this->secretaire = User::factory()->create(['role' => 'secretaire', 'clinic_id' => $this->clinic->id]);
        $medecinUser = User::factory()->create(['role' => 'medecin', 'clinic_id' => $this->clinic->id]);
        $this->medecin = $medecinUser;
        $this->patientUser = User::factory()->create(['role' => 'patient', 'clinic_id' => $this->clinic->id]);

        $specialite = Specialite::create([
            'nom' => 'Gen',
            'tarif_consultation' => 5000,
            'clinic_id' => $this->clinic->id,
        ]);
        Medecin::create([
            'nom' => 'Doc', 'prenom' => 'A', 'telephone' => '0',
            'specialite_id' => $specialite->id, 'tarif_heure' => 1, 'heures_mois' => 1,
            'clinic_id' => $this->clinic->id, 'user_id' => $medecinUser->id,
        ]);

        $this->patient = Patient::create([
            'nom' => 'Pat', 'prenom' => 'A', 'telephone' => '1',
            'est_assure' => false, 'clinic_id' => $this->clinic->id,
            'user_id' => $this->patientUser->id,
        ]);
    }

    public function test_staff_peut_voir_page_documents(): void
    {
        $this->actingAs($this->admin)
            ->get(route('patients.documents.index', $this->patient))
            ->assertOk();

        $this->actingAs($this->secretaire)
            ->get(route('patients.documents.index', $this->patient))
            ->assertOk();

        $this->actingAs($this->medecin)
            ->get(route('patients.documents.index', $this->patient))
            ->assertOk();
    }

    public function test_patient_ne_peut_pas_acceder_page_staff_documents(): void
    {
        $this->actingAs($this->patientUser)
            ->get(route('patients.documents.index', $this->patient))
            ->assertRedirect();
    }

    public function test_admin_peut_uploader_document_information(): void
    {
        $this->actingAs($this->admin)
            ->post(route('patients.documents.store', $this->patient), [
                'nom' => 'Carte ID',
                'type' => 'autre',
                'categorie' => 'informations',
                'fichier' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('documents_patient', [
            'patient_id' => $this->patient->id,
            'nom' => 'Carte ID',
            'categorie' => 'informations',
        ]);
    }

    public function test_admin_ne_peut_pas_uploader_document_medical(): void
    {
        $this->actingAs($this->admin)
            ->post(route('patients.documents.store', $this->patient), [
                'nom' => 'Bilan',
                'type' => 'autre',
                'categorie' => 'medical',
                'fichier' => UploadedFile::fake()->create('bilan.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    public function test_secretaire_ne_peut_pas_uploader_document_medical(): void
    {
        $this->actingAs($this->secretaire)
            ->post(route('patients.documents.store', $this->patient), [
                'nom' => 'Bilan',
                'type' => 'autre',
                'categorie' => 'medical',
                'fichier' => UploadedFile::fake()->create('bilan.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    public function test_medecin_peut_uploader_document_medical(): void
    {
        $this->actingAs($this->medecin)
            ->post(route('patients.documents.store', $this->patient), [
                'nom' => 'Bilan',
                'type' => 'autre',
                'categorie' => 'medical',
                'fichier' => UploadedFile::fake()->create('bilan.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('documents_patient', [
            'patient_id' => $this->patient->id,
            'categorie' => 'medical',
        ]);
    }

    public function test_secretaire_ne_peut_pas_supprimer_document_medical(): void
    {
        $doc = DocumentPatient::create([
            'patient_id' => $this->patient->id,
            'clinic_id' => $this->clinic->id,
            'nom' => 'bilan.pdf',
            'type' => 'autre',
            'categorie' => 'medical',
            'fichier' => 'documents/patients/' . $this->patient->id . '/medical/bilan.pdf',
        ]);

        $this->actingAs($this->secretaire)
            ->delete(route('patients.documents.destroy', [$this->patient, $doc]))
            ->assertForbidden();

        $this->assertDatabaseHas('documents_patient', ['id' => $doc->id, 'deleted_at' => null]);
    }

    public function test_medecin_peut_supprimer_document_medical(): void
    {
        $doc = DocumentPatient::create([
            'patient_id' => $this->patient->id,
            'clinic_id' => $this->clinic->id,
            'nom' => 'bilan.pdf',
            'type' => 'autre',
            'categorie' => 'medical',
            'fichier' => 'documents/patients/' . $this->patient->id . '/medical/bilan.pdf',
        ]);

        $this->actingAs($this->medecin)
            ->delete(route('patients.documents.destroy', [$this->patient, $doc]))
            ->assertRedirect();

        $this->assertSoftDeleted('documents_patient', ['id' => $doc->id]);
    }

    public function test_admin_ne_peut_pas_voir_document_medical(): void
    {
        $doc = DocumentPatient::create([
            'patient_id' => $this->patient->id,
            'clinic_id' => $this->clinic->id,
            'nom' => 'bilan.pdf',
            'type' => 'autre',
            'categorie' => 'medical',
            'fichier' => 'documents/patients/' . $this->patient->id . '/medical/bilan.pdf',
        ]);

        $this->actingAs($this->admin)
            ->get(route('patients.documents.view', [$this->patient, $doc]))
            ->assertForbidden();
    }

    public function test_isolation_cross_clinic_rejette_acces(): void
    {
        $autreClinic = Clinic::create(['name' => 'B', 'slug' => 'b', 'is_active' => true]);
        $autreMedecin = User::factory()->create(['role' => 'medecin', 'clinic_id' => $autreClinic->id]);

        $this->actingAs($autreMedecin)
            ->get(route('patients.documents.index', $this->patient))
            ->assertForbidden();
    }
}
