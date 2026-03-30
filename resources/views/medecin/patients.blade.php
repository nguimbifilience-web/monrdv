@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-blue-900 uppercase italic">Mes Patients</h1>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Patients ayant pris rendez-vous avec vous</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Patient</th>
                    <th class="p-5">Telephone</th>
                    <th class="p-5">Email</th>
                    <th class="p-5">Assurance</th>
                    <th class="p-5">Quartier</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-md shadow-blue-100">
                                {{ strtoupper(substr($patient->prenom, 0, 1)) }}{{ strtoupper(substr($patient->nom, 0, 1)) }}
                            </div>
                            <span class="font-black text-blue-900 text-xs uppercase">{{ $patient->nom }} {{ $patient->prenom }}</span>
                        </div>
                    </td>
                    <td class="p-5 text-xs font-bold text-gray-600">{{ $patient->telephone }}</td>
                    <td class="p-5 text-[10px] text-gray-400">{{ $patient->email ?? '—' }}</td>
                    <td class="p-5">
                        @if($patient->est_assure && $patient->assurance)
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black">
                                <i class="fas fa-shield-alt mr-1"></i>{{ $patient->assurance->nom }}
                            </span>
                        @else
                            <span class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-[10px] font-black">Non assure</span>
                        @endif
                    </td>
                    <td class="p-5 text-xs text-gray-500">{{ $patient->quartier ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <i class="fas fa-user-slash text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucun patient</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($patients->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
