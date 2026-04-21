<?php

namespace App\Http\Controllers;

use App\Models\DocumentPatient;
use App\Models\FeuilleExamen;
use App\Models\Ordonnance;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentPatientController extends Controller
{
    public function index(Patient $patient)
    {
        $user = auth()->user();

        abort_unless(
            $user->can('viewCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_INFO])
            || $user->can('viewCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_MEDICAL]),
            403
        );

        $peutVoirInfo = $user->can('viewCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_INFO]);
        $peutVoirMedical = $user->can('viewCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_MEDICAL]);
        $peutUploadInfo = $user->can('uploadCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_INFO]);
        $peutUploadMedical = $user->can('uploadCategorie', [DocumentPatient::class, $patient, DocumentPatient::CATEGORIE_MEDICAL]);

        $documentsInfo = $peutVoirInfo
            ? DocumentPatient::where('patient_id', $patient->id)->informations()->latest()->get()
            : collect();

        $documentsMedical = $peutVoirMedical
            ? DocumentPatient::where('patient_id', $patient->id)->medical()->latest()->get()
            : collect();

        $ordonnances = $peutVoirMedical
            ? Ordonnance::with(['medecin.specialite', 'lignes'])
                ->where('patient_id', $patient->id)
                ->orderByDesc('date')
                ->get()
            : collect();

        $feuilles = $peutVoirMedical
            ? FeuilleExamen::with(['medecin.specialite', 'lignes'])
                ->where('patient_id', $patient->id)
                ->orderByDesc('date')
                ->get()
            : collect();

        return view('patients.documents', compact(
            'patient',
            'documentsInfo',
            'documentsMedical',
            'ordonnances',
            'feuilles',
            'peutVoirInfo',
            'peutVoirMedical',
            'peutUploadInfo',
            'peutUploadMedical',
        ));
    }

    public function store(Request $request, Patient $patient)
    {
        $user = auth()->user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:assurance,ordonnance,autre',
            'categorie' => 'required|in:informations,medical',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        abort_unless(
            $user->can('uploadCategorie', [DocumentPatient::class, $patient, $request->categorie]),
            403
        );

        $path = $request->file('fichier')->store(
            "documents/patients/{$patient->id}/{$request->categorie}",
            'local'
        );

        DocumentPatient::create([
            'patient_id' => $patient->id,
            'nom' => $request->nom,
            'type' => $request->type,
            'categorie' => $request->categorie,
            'fichier' => $path,
        ]);

        return back()->with('success', 'Document ajoute.');
    }

    public function view(Patient $patient, DocumentPatient $document)
    {
        $this->ensureBelongs($patient, $document);
        $this->authorize('view', $document);

        if (!Storage::disk('local')->exists($document->fichier)) {
            abort(404);
        }

        return Storage::disk('local')->response($document->fichier, $document->nom);
    }

    public function download(Patient $patient, DocumentPatient $document)
    {
        $this->ensureBelongs($patient, $document);
        $this->authorize('download', $document);

        if (!Storage::disk('local')->exists($document->fichier)) {
            abort(404);
        }

        return Storage::disk('local')->download($document->fichier, $document->nom);
    }

    public function destroy(Patient $patient, DocumentPatient $document)
    {
        $this->ensureBelongs($patient, $document);
        $this->authorize('delete', $document);

        Storage::disk('local')->delete($document->fichier);
        $document->delete();

        return back()->with('success', 'Document supprime.');
    }

    private function ensureBelongs(Patient $patient, DocumentPatient $document): void
    {
        abort_unless($document->patient_id === $patient->id, 404);
    }
}
