@extends('layouts.master')

@section('content')
<div class="container py-4">
    <h4 class="font-weight-bold mb-4" style="color: #1a357c;">Choisir un médecin pour gérer le planning</h4>
    
    <div class="row">
        @foreach($medecins as $medecin)
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-md fa-3x text-primary"></i>
                    </div>
                    <h5 class="font-weight-bold">Dr. {{ $medecin->nom }}</h5>
                    <p class="text-muted">{{ $medecin->specialite->nom ?? 'Généraliste' }}</p>
                    
                    {{-- Lien vers le calendrier individuel --}}
                    <a href="{{ route('medecins.calendar', $medecin->id) }}" class="btn btn-warning btn-block font-weight-bold">
                        <i class="fas fa-calendar-alt mr-2"></i> VOIR LE CALENDRIER
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection