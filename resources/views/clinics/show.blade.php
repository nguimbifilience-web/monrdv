@extends('layouts.master')

@section('content')
@php
    $statusBadge = $clinic->is_blocked
        ? ['bg-red-100 text-red-600', 'Suspendu', 'fa-ban']
        : ($clinic->is_active ? ['bg-green-100 text-green-600', 'Actif', 'fa-check-circle'] : ['bg-gray-100 text-gray-500', 'Inactif', 'fa-pause-circle']);
@endphp

{{-- Header --}}
<a href="{{ route('clinics.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 hover:text-blue-600 uppercase tracking-widest mb-4">
    <i class="fas fa-arrow-left"></i> Retour aux cliniques
</a>

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-5 md:p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div class="flex items-start gap-4 min-w-0">
            @if($clinic->logo_url)
                <img src="{{ $clinic->logo_url }}" class="w-16 h-16 md:w-20 md:h-20 rounded-2xl object-cover shrink-0">
            @else
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex items-center justify-center text-white font-black text-xl shrink-0" style="background-color: {{ $clinic->getPrimaryColorOrDefault() }}">
                    {{ strtoupper(substr($clinic->name, 0, 2)) }}
                </div>
            @endif
            <div class="min-w-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-black text-blue-900 uppercase italic truncate">{{ $clinic->name }}</h1>
                    <span class="{{ $statusBadge[0] }} px-3 py-1 rounded-full text-[9px] font-black uppercase whitespace-nowrap">
                        <i class="fas {{ $statusBadge[2] }} mr-1"></i>{{ $statusBadge[1] }}
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $clinic->city ?? '—' }}@if($clinic->address) — {{ $clinic->address }}@endif
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    @if($clinic->phone)<i class="fas fa-phone mr-1"></i>{{ $clinic->phone }}@endif
                    @if($clinic->email)<span class="ml-3"><i class="fas fa-envelope mr-1"></i>{{ $clinic->email }}</span>@endif
                </p>
                @if($clinic->plan)
                    <p class="text-[10px] font-bold text-indigo-600 mt-2 uppercase tracking-widest">
                        <i class="fas fa-gem mr-1"></i>Plan {{ $clinic->plan->name }}
                        @if($clinic->subscription_expires_at)
                            — expire le {{ $clinic->subscription_expires_at->format('d/m/Y') }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Onglets --}}
@php
    $tabs = [
        'apercu' => ['Aperçu', 'fa-th-large'],
        'medecins' => ['Médecins', 'fa-user-md'],
        'patients' => ['Patients', 'fa-user-injured'],
        'rendezvous' => ['Rendez-vous', 'fa-calendar-check'],
        'specialites' => ['Spécialités', 'fa-stethoscope'],
        'assurances' => ['Assurances', 'fa-shield-alt'],
    ];
@endphp
<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-6">
    <div class="flex overflow-x-auto border-b border-gray-50">
        @foreach($tabs as $key => [$label, $icon])
            <a href="{{ route('clinics.show', ['clinic' => $clinic, 'tab' => $key]) }}"
               class="shrink-0 px-5 md:px-6 py-4 text-[10px] font-black uppercase tracking-widest transition-colors border-b-2 {{ $tab === $key ? 'text-blue-600 border-blue-600' : 'text-gray-400 border-transparent hover:text-blue-600' }}">
                <i class="fas {{ $icon }} mr-2"></i>{{ $label }}
            </a>
        @endforeach
    </div>

    <div class="p-5 md:p-8">
        @if($tab === 'apercu')
            {{-- Aperçu --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">
                <div class="bg-blue-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Médecins</p>
                    <p class="text-2xl md:text-3xl font-black text-blue-600">{{ $clinic->medecins_count }}</p>
                </div>
                <div class="bg-cyan-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-cyan-400 uppercase tracking-widest">Patients</p>
                    <p class="text-2xl md:text-3xl font-black text-cyan-600">{{ $clinic->patients_count }}</p>
                </div>
                <div class="bg-orange-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-orange-400 uppercase tracking-widest">RDV (mois)</p>
                    <p class="text-2xl md:text-3xl font-black text-orange-600">{{ $rdvMonth ?? 0 }}</p>
                </div>
                <div class="bg-green-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-green-400 uppercase tracking-widest">Revenus (mois)</p>
                    <p class="text-lg md:text-2xl font-black text-green-600 truncate">{{ number_format($revenueMonth ?? 0, 0, ',', ' ') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Spécialités actives</p>
                    <p class="text-2xl font-black text-blue-900">{{ $clinic->specialites_count }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Assurances acceptées</p>
                    <p class="text-2xl font-black text-blue-900">{{ $clinic->assurances_count }}</p>
                </div>
            </div>

        @elseif($tab === 'medecins')
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[600px]">
                    <thead class="border-b border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-400">
                            <th class="py-3 px-2">Nom</th>
                            <th class="py-3 px-2">Spécialité</th>
                            <th class="py-3 px-2">Téléphone</th>
                            <th class="py-3 px-2 text-right">Tarif/h</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($medecins as $med)
                            <tr>
                                <td class="py-3 px-2 text-xs font-bold text-blue-900">Dr. {{ $med->nom }} {{ $med->prenom }}</td>
                                <td class="py-3 px-2 text-xs text-gray-600">{{ $med->specialite?->nom ?? '—' }}</td>
                                <td class="py-3 px-2 text-xs text-gray-500">{{ $med->telephone ?? '—' }}</td>
                                <td class="py-3 px-2 text-xs text-right text-green-600 font-black">{{ number_format($med->tarif_heure ?? 0, 0, ',', ' ') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-gray-400 text-xs italic">Aucun médecin</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($medecins->hasPages()) <div class="mt-4">{{ $medecins->links() }}</div> @endif

        @elseif($tab === 'patients')
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[600px]">
                    <thead class="border-b border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-400">
                            <th class="py-3 px-2">Nom</th>
                            <th class="py-3 px-2">Téléphone</th>
                            <th class="py-3 px-2">Quartier</th>
                            <th class="py-3 px-2 text-center">Assuré</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($patients as $pat)
                            <tr>
                                <td class="py-3 px-2 text-xs font-bold text-blue-900">{{ $pat->nom }} {{ $pat->prenom }}</td>
                                <td class="py-3 px-2 text-xs text-gray-500">{{ $pat->telephone ?? '—' }}</td>
                                <td class="py-3 px-2 text-xs text-gray-500">{{ $pat->quartier ?? '—' }}</td>
                                <td class="py-3 px-2 text-center">
                                    @if($pat->est_assure)
                                        <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded text-[9px] font-black uppercase">Oui</span>
                                    @else
                                        <span class="text-gray-300 text-[9px]">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-gray-400 text-xs italic">Aucun patient</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($patients->hasPages()) <div class="mt-4">{{ $patients->links() }}</div> @endif

        @elseif($tab === 'rendezvous')
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-[600px]">
                    <thead class="border-b border-gray-50">
                        <tr class="text-[9px] font-black uppercase text-gray-400">
                            <th class="py-3 px-2">Date</th>
                            <th class="py-3 px-2">Patient</th>
                            <th class="py-3 px-2">Médecin</th>
                            <th class="py-3 px-2 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($rendezvous as $rdv)
                            @php
                                $statutColor = match($rdv->statut) {
                                    'confirme' => 'bg-green-50 text-green-600',
                                    'annule' => 'bg-red-50 text-red-600',
                                    'honore' => 'bg-blue-50 text-blue-600',
                                    default => 'bg-gray-50 text-gray-500',
                                };
                            @endphp
                            <tr>
                                <td class="py-3 px-2 text-xs font-bold text-blue-900">{{ $rdv->date_rv?->format('d/m/Y') }} @if($rdv->heure_rv) {{ $rdv->heure_rv }}@endif</td>
                                <td class="py-3 px-2 text-xs text-gray-600">{{ $rdv->patient?->nom ?? '—' }} {{ $rdv->patient?->prenom }}</td>
                                <td class="py-3 px-2 text-xs text-gray-500">{{ $rdv->medecin ? 'Dr. ' . $rdv->medecin->nom : '—' }}</td>
                                <td class="py-3 px-2 text-center">
                                    <span class="{{ $statutColor }} px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $rdv->statut }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-gray-400 text-xs italic">Aucun rendez-vous</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rendezvous->hasPages()) <div class="mt-4">{{ $rendezvous->links() }}</div> @endif

        @elseif($tab === 'specialites')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($specialites as $spec)
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas {{ $spec->icone ?? 'fa-stethoscope' }}"></i>
                        </div>
                        <div>
                            <p class="font-black text-blue-900 text-xs uppercase">{{ $spec->nom }}</p>
                            @if($spec->tarif_consultation)
                                <p class="text-[10px] text-gray-500">{{ number_format($spec->tarif_consultation, 0, ',', ' ') }} XAF</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400 text-xs italic">Aucune spécialité</div>
                @endforelse
            </div>

        @elseif($tab === 'assurances')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($assurances as $ass)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="font-black text-blue-900 text-xs uppercase">{{ $ass->nom }}</p>
                        @if($ass->taux_couverture)
                            <p class="text-[10px] text-gray-500 mt-1">Taux : {{ $ass->taux_couverture }}%</p>
                        @endif
                        @if($ass->nom_referent)
                            <p class="text-[10px] text-gray-400">Réf. : {{ $ass->nom_referent }}</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400 text-xs italic">Aucune assurance</div>
                @endforelse
            </div>
        @endif
    </div>
</div>
@endsection
