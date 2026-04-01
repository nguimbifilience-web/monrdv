@extends('layouts.master')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-blue-900 uppercase italic">Dashboard Super Admin</h1>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Vue globale de toutes les cliniques</p>
</div>

{{-- STATS GLOBALES --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-hospital text-red-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_clinics'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Cliniques</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-check-circle text-green-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['active_clinics'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Actives</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-users text-blue-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_users'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Utilisateurs</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-cyan-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-user-injured text-cyan-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_patients'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Patients</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-user-md text-purple-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_medecins'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Medecins</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-5">
        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mb-3">
            <i class="fas fa-calendar-check text-orange-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $stats['total_rdv_today'] }}</p>
        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">RDV aujourd'hui</p>
    </div>
</div>

{{-- TABLEAU DES CLINIQUES --}}
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-8 border-b border-gray-50">
        <h2 class="text-lg font-black text-blue-900 uppercase">Cliniques</h2>
    </div>
    <table class="w-full text-left">
        <thead class="bg-gray-50/50 border-b border-gray-50">
            <tr class="text-[9px] font-black uppercase text-gray-300">
                <th class="p-5">Clinique</th>
                <th class="p-5 text-center">Statut</th>
                <th class="p-5 text-center">Utilisateurs</th>
                <th class="p-5 text-center">Patients</th>
                <th class="p-5 text-center">Medecins</th>
                <th class="p-5 text-center">RDV</th>
                <th class="p-5 text-center">Consultations</th>
                <th class="p-5 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($clinics as $clinic)
            <tr class="hover:bg-gray-50/30 transition-colors">
                <td class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br {{ $clinic->is_active ? 'from-blue-500 to-blue-600' : 'from-gray-400 to-gray-500' }} rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-hospital text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-800">{{ $clinic->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $clinic->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="p-5 text-center">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                        {{ $clinic->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        {{ $clinic->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->users_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->patients_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->medecins_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->rendezvous_count }}</td>
                <td class="p-5 text-center font-bold text-gray-700">{{ $clinic->consultations_count }}</td>
                <td class="p-5 text-center">
                    <a href="{{ route('clinics.index') }}" class="text-blue-500 hover:text-blue-700 text-xs font-bold">
                        <i class="fas fa-cog"></i> Gerer
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
