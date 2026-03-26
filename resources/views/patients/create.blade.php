@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Ajouter un nouveau Patient</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('patients.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email (Optionnel)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quartier</label>
                    <input type="text" name="quartier" class="form-control" value="{{ old('quartier') }}">
                </div>

                <div class="row border p-3 rounded bg-light mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-primary fw-bold">Le patient est-il assuré ?</label>
                        <select name="est_assure" id="est_assure_select" class="form-select" onchange="toggleAssurance(this.value)">
                            <option value="0" {{ old('est_assure') == '0' ? 'selected' : '' }}>Non</option>
                            <option value="1" {{ old('est_assure') == '1' ? 'selected' : '' }}>Oui</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="bloc_assurance" style="display: none;">
                        <label class="form-label">Choisir l'Assurance</label>
                        <select name="assurance_id" class="form-select">
                            <option value="">-- Sélectionner --</option>
                            @foreach($assurances as $a)
                                <option value="{{ $a->id }}" {{ old('assurance_id') == $a->id ? 'selected' : '' }}>{{ $a->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Médecin traitant</label>
                    <select name="medecin_id" class="form-select">
                        <option value="">-- Aucun médecin assigné --</option>
                        @foreach($medecins as $m)
                            <option value="{{ $m->id }}" {{ old('medecin_id') == $m->id ? 'selected' : '' }}>Dr. {{ $m->nom }} {{ $m->prenom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-5">Enregistrer le patient</button>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleAssurance(val) {
    const bloc = document.getElementById('bloc_assurance');
    bloc.style.display = (val === "1") ? "block" : "none";
}
// Exécuter au chargement pour gérer le "old value"
window.onload = function() {
    toggleAssurance(document.getElementById('est_assure_select').value);
};
</script>
@endsection