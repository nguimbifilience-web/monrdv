@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header Style Journal -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; background-color: #1a357c; color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold mb-0 text-uppercase">Nouveau Rendez-vous</h5>
                            <small class="text-white-50">Remplissez les informations pour planifier une consultation</small>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm font-weight-bold">
                            <i class="fas fa-arrow-left mr-1"></i> RETOUR AU TABLEAU DE BORD
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-5">
                    <form action="{{ route('rendezvous.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Sélection du Patient -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Patient</label>
                                <select name="patient_id" class="form-control form-control-lg @error('patient_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Sélection du Médecin -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Médecin</label>
                                <select name="medecin_id" class="form-control form-control-lg @error('medecin_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un médecin...</option>
                                    @foreach($medecins as $medecin)
                                        <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }} ({{ $medecin->specialite->nom ?? 'Généraliste' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date du RDV -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Date du rendez-vous</label>
                                <input type="date" name="date_rv" class="form-control form-control-lg" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Heure du RDV -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Heure</label>
                                <input type="time" name="heure_rv" class="form-control form-control-lg" required>
                            </div>

                            <!-- Motif / Notes -->
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">Motif de consultation (Optionnel)</label>
                                <textarea name="motif" class="form-control" rows="3" placeholder="Ex: Contrôle annuel, Urgence..."></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-light btn-lg mr-3 px-4">Réinitialiser</button>
                            <button type="submit" class="btn btn-warning btn-lg px-5 font-weight-bold shadow">
                                <i class="fas fa-save mr-2"></i> ENREGISTRER LE RDV
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection