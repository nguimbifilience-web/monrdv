@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Tableau de Bord</h1>
    <p class="text-sm text-gray-500">Bienvenue, {{ auth()->user()->name }} — MonRDV</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

    {{-- Bouton Rendez-vous --}}
    <a href="{{ route('rendezvous.index') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-orange-300 transition-all">
        <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-500 transition">
            <i class="fas fa-calendar-check text-2xl text-orange-500 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Rendez-vous</h2>
        <p class="text-sm text-gray-500 mb-4">Planifier, consulter et gérer les rendez-vous médicaux</p>
        <div class="flex items-center justify-between">
            <span class="bg-orange-100 text-orange-600 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbRendezvous }} aujourd'hui
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-orange-500 transition"></i>
        </div>
    </a>

    {{-- Bouton Planning Médecin --}}
    <a href="{{ route('medecins.schedule') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-blue-300 transition-all">
        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-900 transition">
            <i class="fas fa-user-md text-2xl text-blue-900 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Planning Médecin</h2>
        <p class="text-sm text-gray-500 mb-4">Gérer les jours de travail et disponibilités des médecins</p>
        <div class="flex items-center justify-between">
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbMedecins }} médecins
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-900 transition"></i>
        </div>
    </a>

    {{-- Bouton Patients --}}
    <a href="{{ route('patients.index') }}"
       class="group bg-white rounded-2xl shadow-md border border-gray-100 p-8 hover:shadow-2xl hover:border-green-300 transition-all">
        <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-600 transition">
            <i class="fas fa-user-injured text-2xl text-green-600 group-hover:text-white transition"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Patients</h2>
        <p class="text-sm text-gray-500 mb-4">Créer, modifier et consulter les dossiers patients</p>
        <div class="flex items-center justify-between">
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                {{ $nbPatients }} patients
            </span>
            <i class="fas fa-arrow-right text-gray-300 group-hover:text-green-600 transition"></i>
        </div>
    </a>

</div>

{{-- Message flash --}}
@if(session('success'))
<div id="flash-msg" class="fixed bottom-6 right-6 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl font-bold text-sm z-50 flex items-center gap-2">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('flash-msg')?.remove(), 3000)</script>
@endif
@endsection
