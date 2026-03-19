@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded shadow-lg max-w-lg mx-auto">
    <h1 class="text-xl font-bold mb-4">Détails du Rendez-vous</h1>
    <p><strong>Patient :</strong> {{ $rendezvous->patient->nom_complet }}</p>
    <p><strong>Médecin :</strong> Dr. {{ $rendezvous->medecin->nom }}</p>
    <p><strong>Date :</strong> {{ $rendezvous->date_rdv }}</p>
    <p><strong>Motif :</strong> {{ $rendezvous->motif ?? 'Non précisé' }}</p>
    
    <div class="mt-6">
        <a href="{{ route('rendezvous.index') }}" class="text-blue-500 underline">Retour au planning</a>
    </div>
</div>
@endsection