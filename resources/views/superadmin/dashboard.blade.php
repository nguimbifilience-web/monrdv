@extends('layouts.master')

@section('content')
<div class="mb-6 md:mb-8">
    <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Vue d'ensemble des cliniques</h1>
    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Dashboard Super Admin — statistiques par clinique</p>
</div>

{{-- Filtres + toggle d'affichage --}}
<form method="GET" action="{{ route('superadmin.dashboard') }}" class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-4 md:p-6 mb-6 md:mb-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une clinique..."
                   class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        </div>
        <select name="status" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les cliniques</option>
            <option value="active" @selected(request('status') === 'active')>Actives</option>
            <option value="suspended" @selected(request('status') === 'suspended')>Suspendues</option>
        </select>
        <select name="city" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les villes</option>
            @foreach($cities as $cityName)
                <option value="{{ $cityName }}" @selected(request('city') === $cityName)>{{ $cityName }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-black px-4 py-3 rounded-xl text-xs uppercase tracking-widest transition-colors">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            {{-- Toggle cards/table --}}
            <div class="flex bg-gray-100 rounded-xl p-1">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'cards']) }}" class="px-3 py-2 rounded-lg text-xs font-black transition-all {{ $view === 'cards' ? 'bg-white text-blue-600 shadow' : 'text-gray-400' }}" title="Vue cartes">
                    <i class="fas fa-th"></i>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="px-3 py-2 rounded-lg text-xs font-black transition-all {{ $view === 'table' ? 'bg-white text-blue-600 shadow' : 'text-gray-400' }}" title="Vue tableau">
                    <i class="fas fa-list"></i>
                </a>
            </div>
        </div>
    </div>
</form>

@if($clinics->isEmpty())
    <div class="bg-white rounded-3xl shadow-sm border border-gray-50 p-12 text-center">
        <i class="fas fa-hospital text-5xl text-gray-200 mb-4"></i>
        <p class="text-gray-400 font-bold uppercase text-sm">Aucune clinique ne correspond aux filtres</p>
    </div>
@elseif($view === 'cards')

    {{-- MODE CARTES --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        @foreach($clinics as $clinic)
            @php
                $statusBadge = $clinic->is_blocked
                    ? ['bg-red-100 text-red-600', 'Suspendu', 'fa-ban']
                    : ($clinic->is_active ? ['bg-green-100 text-green-600', 'Actif', 'fa-check-circle'] : ['bg-gray-100 text-gray-500', 'Inactif', 'fa-pause-circle']);
            @endphp
            <a href="{{ route('clinics.show', $clinic) }}" class="block bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all">
                <div class="p-5 md:p-6 border-b border-gray-50">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($clinic->logo_url)
                                <img src="{{ $clinic->logo_url }}" class="w-12 h-12 rounded-xl object-cover shrink-0">
                            @else
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-black text-sm shrink-0" style="background-color: {{ $clinic->getPrimaryColorOrDefault() }}">
                                    {{ strtoupper(substr($clinic->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-black text-blue-900 text-sm md:text-base uppercase truncate">{{ $clinic->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold">{{ $clinic->city ?? '—' }}</p>
                            </div>
                        </div>
                        <span class="{{ $statusBadge[0] }} px-3 py-1 rounded-full text-[9px] font-black uppercase whitespace-nowrap shrink-0">
                            <i class="fas {{ $statusBadge[2] }} mr-1"></i>{{ $statusBadge[1] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-blue-50 rounded-xl p-3">
                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Médecins</p>
                            <p class="text-xl font-black text-blue-600">{{ $clinic->medecins_count }}</p>
                        </div>
                        <div class="bg-cyan-50 rounded-xl p-3">
                            <p class="text-[9px] font-black text-cyan-400 uppercase tracking-widest">Patients</p>
                            <p class="text-xl font-black text-cyan-600">{{ $clinic->patients_count }}</p>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-3">
                            <p class="text-[9px] font-black text-orange-400 uppercase tracking-widest">RDV (mois)</p>
                            <p class="text-xl font-black text-orange-600">{{ $clinic->rdv_this_month }}</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3">
                            <p class="text-[9px] font-black text-green-400 uppercase tracking-widest">Revenus (mois)</p>
                            <p class="text-sm md:text-base font-black text-green-600 truncate">{{ number_format($clinic->revenue_this_month, 0, ',', ' ') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-5 md:px-6 py-3 text-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600">
                        Voir les détails <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

@else

    {{-- MODE TABLEAU --}}
    <div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[720px]">
                <thead class="bg-gray-50/50 border-b border-gray-50">
                    <tr class="text-[9px] font-black uppercase text-gray-400">
                        <th class="p-4">Clinique</th>
                        <th class="p-4">Ville</th>
                        <th class="p-4 text-center">Statut</th>
                        <th class="p-4 text-center">Médecins</th>
                        <th class="p-4 text-center">Patients</th>
                        <th class="p-4 text-center">RDV (mois)</th>
                        <th class="p-4 text-right">Revenus (mois)</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($clinics as $clinic)
                        @php
                            $statusBadge = $clinic->is_blocked
                                ? ['bg-red-100 text-red-600', 'Suspendu']
                                : ($clinic->is_active ? ['bg-green-100 text-green-600', 'Actif'] : ['bg-gray-100 text-gray-500', 'Inactif']);
                        @endphp
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    @if($clinic->logo_url)
                                        <img src="{{ $clinic->logo_url }}" class="w-8 h-8 rounded-lg object-cover shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-[10px] shrink-0" style="background-color: {{ $clinic->getPrimaryColorOrDefault() }}">
                                            {{ strtoupper(substr($clinic->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="font-black text-blue-900 text-xs uppercase">{{ $clinic->name }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-xs font-bold text-gray-600">{{ $clinic->city ?? '—' }}</td>
                            <td class="p-4 text-center">
                                <span class="{{ $statusBadge[0] }} px-3 py-1 rounded-full text-[9px] font-black uppercase">{{ $statusBadge[1] }}</span>
                            </td>
                            <td class="p-4 text-center font-black text-blue-600 text-sm">{{ $clinic->medecins_count }}</td>
                            <td class="p-4 text-center font-black text-cyan-600 text-sm">{{ $clinic->patients_count }}</td>
                            <td class="p-4 text-center font-black text-orange-600 text-sm">{{ $clinic->rdv_this_month }}</td>
                            <td class="p-4 text-right font-black text-green-600 text-sm">{{ number_format($clinic->revenue_this_month, 0, ',', ' ') }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('clinics.show', $clinic) }}" class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg inline-flex items-center justify-center hover:bg-blue-500 hover:text-white transition-colors" title="Voir">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif
@endsection
