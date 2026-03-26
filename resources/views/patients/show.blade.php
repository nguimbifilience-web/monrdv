@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('patients.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
                <div class="bg-primary text-white rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h3 class="fw-bold text-uppercase mb-1">{{ $patient->nom }}</h3>
                <p class="text-muted">{{ $patient->prenom }}</p>
                <hr>
                <div class="text-start">
                    <p class="mb-1 text-muted small text-uppercase fw-bold">Téléphone</p>
                    <p class="fw-bold text-dark">{{ $patient->telephone }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Détails du Dossier</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold d-block">Quartier</label>
                            <span class="fs-5 fw-medium text-dark">{{ $patient->quartier ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold d-block">Statut Assurance</label>
                            @if($patient->est_assure)
                                <span class="badge bg-success-subtle text-success fs-6 rounded-pill px-3 py-2 border border-success-subtle">
                                    <i class="bi bi-check-circle-fill me-1"></i>Assuré ({{ $patient->assurance->nom ?? 'Inconnue' }})
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 rounded-pill px-3 py-2 border border-danger-subtle">
                                    <i class="bi bi-x-circle-fill me-1"></i>Non assuré
                                </span>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <div class="p-3 rounded-4 bg-light border border-dashed text-center">
                                <label class="text-muted small text-uppercase fw-bold d-block mb-2">Médecin Traitant Assigné</label>
                                <span class="fs-4 text-primary fw-bold">
                                    <i class="bi bi-stethoscope me-2"></i>
                                    {{ $patient->medecin ? 'Dr. ' . $patient->medecin->nom . ' ' . $patient->medecin->prenom : 'Aucun médecin assigné' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 text-end">
                    <button class="btn btn-dark rounded-pill px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection