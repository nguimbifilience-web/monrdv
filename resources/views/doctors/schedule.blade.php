@extends('layouts.master')

@section('content')
<div class="container-fluid p-4">
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 font-weight-bold text-dark">PROGRAMME MENSUEL DES MÉDECINS</h4>
            <button class="btn btn-outline-primary btn-sm">Ajouter un Horaire</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Médecin</th>
                            <th>Spécialité</th>
                            <th>Jours de présence</th>
                            <th>Tranche Horaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medecins as $medecin)
                        <tr>
                            <td class="font-weight-bold">Dr. {{ $medecin->nom }}</td>
                            <td><span class="badge badge-info text-white">{{ $medecin->specialite->nom ?? 'Généraliste' }}</span></td>
                            <td>Lundi, Mercredi, Vendredi</td> <!-- À rendre dynamique selon ta table -->
                            <td>08:00 - 16:00</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection