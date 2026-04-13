@extends('layouts.master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Gestion des accès</h1>
    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Rôles, permissions et cliniques bloquées</p>
</div>

{{-- Matrice des rôles --}}
<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden mb-6">
    <div class="p-5 md:p-6 border-b border-gray-50">
        <h2 class="font-black text-blue-900 text-base md:text-lg uppercase"><i class="fas fa-th mr-2 text-blue-500"></i>Matrice des rôles</h2>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Permissions par module (lecture seule)</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[640px]">
            <thead class="bg-gray-50/50">
                <tr class="text-[9px] font-black uppercase text-gray-400">
                    <th class="p-4">Module</th>
                    @foreach($roles as $roleKey => $roleLabel)
                        <th class="p-4 text-center">{{ $roleLabel }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($modules as $modKey => $modLabel)
                    <tr>
                        <td class="p-4 text-xs font-bold text-blue-900">{{ $modLabel }}</td>
                        @foreach(array_keys($roles) as $roleKey)
                            @php
                                $level = $matrix[$roleKey][$modKey] ?? 'none';
                                $badge = match($level) {
                                    'full' => ['bg-green-100 text-green-600', 'Total'],
                                    'own' => ['bg-blue-100 text-blue-600', 'Le sien'],
                                    'read' => ['bg-yellow-100 text-yellow-600', 'Lecture'],
                                    default => ['bg-gray-100 text-gray-400', '—'],
                                };
                            @endphp
                            <td class="p-4 text-center">
                                <span class="{{ $badge[0] }} px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $badge[1] }}</span>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Cliniques bloquées --}}
<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
    <div class="p-5 md:p-6 border-b border-gray-50">
        <h2 class="font-black text-blue-900 text-base md:text-lg uppercase"><i class="fas fa-ban mr-2 text-red-500"></i>Cliniques bloquées</h2>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Utilisateurs actuellement verrouillés</p>
    </div>
    <div class="p-5 md:p-6">
        @if($lockedClinics->isEmpty())
            <p class="text-center py-8 text-gray-400 text-xs italic">Aucune clinique bloquée</p>
        @else
            <div class="space-y-3">
                @foreach($lockedClinics as $clinic)
                    <div class="bg-red-50 border border-red-100 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <p class="font-black text-red-700 text-sm uppercase">{{ $clinic->name }}</p>
                            <p class="text-[10px] text-red-500">{{ $clinic->users_count }} utilisateur(s) bloqué(s)</p>
                            @if($clinic->blocked_reason)
                                <p class="text-[10px] text-gray-500 mt-1 italic">« {{ $clinic->blocked_reason }} »</p>
                            @endif
                        </div>
                        <form action="{{ route('clinics.unblock', $clinic) }}" method="POST" onsubmit="return confirm('Débloquer cette clinique ?')">
                            @csrf @method('PATCH')
                            <button class="bg-green-500 hover:bg-green-600 text-white font-black px-4 py-2 rounded-xl text-[10px] uppercase tracking-widest"><i class="fas fa-unlock mr-1"></i>Débloquer</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
