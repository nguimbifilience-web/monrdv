@extends('layouts.master')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; background-color: #0d1e3a; color: white;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="font-weight-bold mb-0 text-uppercase">Modifier le Rendez-vous</h5>
                            <small class="text-white-50">Modification du RDV de {{ $rendezvous->patient->nom }}</small>
                        </div>
                        <a href="{{ route('rendezvous.index') }}" class="btn btn-outline-light btn-sm font-weight-bold">
                            <i class="fas fa-arrow-left mr-1"></i> ANNULER
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-5">
                    <form action="{{ route('rendezvous.update', $rendezvous->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- CRUCIAL : Indique à Laravel que c'est une mise à jour --}}
                        
                        <div class="row">
                            <!-- Patient (pré-sélectionné) -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Patient</label>
                                <select name="patient_id" class="form-control form-control-lg" required>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ $rendezvous->patient_id == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->nom }} {{ $patient->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Médecin (pré-sélectionné) -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Médecin</label>
                                <select name="medecin_id" class="form-control form-control-lg" required>
                                    @foreach($medecins as $medecin)
                                        <option value="{{ $medecin->id }}" {{ $rendezvous->medecin_id == $medecin->id ? 'selected' : '' }}>
                                            Dr. {{ $medecin->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Date du RDV</label>
                                <input type="date" name="date_rv" class="form-control form-control-lg" value="{{ $rendezvous->date_rv }}" required>
                            </div>

                            <!-- Heure -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark">Heure</label>
                                <input type="time" name="heure_rv" class="form-control form-control-lg" value="{{ $rendezvous->heure_rv }}" required>
                            </div>

                            <!-- Motif -->
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark">Motif</label>
                                <textarea name="motif" class="form-control" rows="3">{{ $rendezvous->motif }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5 font-weight-bold shadow">
                                <i class="fas fa-sync-alt mr-2"></i> METTRE À JOUR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection