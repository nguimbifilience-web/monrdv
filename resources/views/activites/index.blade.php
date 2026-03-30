@extends('layouts.master')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-blue-900 uppercase italic">Traçabilité</h1>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Journal des modifications</p>
        </div>
    </div>

    {{-- FILTRES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-50 p-4 mb-6">
        <form action="{{ route('activites.index') }}" method="GET" class="flex items-center gap-4">
            <select name="user_id" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[180px]">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            <select name="action" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900 min-w-[160px]">
                <option value="">Toutes les actions</option>
                <option value="creation" {{ request('action') == 'creation' ? 'selected' : '' }}>Création</option>
                <option value="modification" {{ request('action') == 'modification' ? 'selected' : '' }}>Modification</option>
                <option value="suppression" {{ request('action') == 'suppression' ? 'selected' : '' }}>Suppression</option>
                <option value="annulation" {{ request('action') == 'annulation' ? 'selected' : '' }}>Annulation</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="bg-gray-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-blue-900">
            <button type="submit" class="bg-blue-900 text-white px-6 py-3 rounded-xl font-black uppercase text-[10px]">
                <i class="fas fa-filter mr-1"></i> Filtrer
            </button>
            @if(request()->anyFilled(['user_id', 'action', 'date']))
                <a href="{{ route('activites.index') }}" class="text-gray-400 hover:text-red-400 text-xs font-bold"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-5">Date / Heure</th>
                    <th class="p-5">Utilisateur</th>
                    <th class="p-5">Action</th>
                    <th class="p-5">Description</th>
                    <th class="p-5">Élément</th>
                    <th class="p-5">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-5">
                        <div class="font-black text-blue-900 text-xs">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div class="text-[10px] text-gray-400 font-bold">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="p-5">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 {{ $log->user->isAdmin() ? 'bg-orange-500' : 'bg-blue-500' }} rounded-lg flex items-center justify-center text-white text-[10px] font-black">
                                {{ strtoupper(substr($log->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-xs font-black text-blue-900">{{ $log->user->name }}</span>
                                <p class="text-[9px] text-gray-400">{{ ucfirst($log->user->role) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-5">
                        @if($log->action === 'creation')
                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Création</span>
                        @elseif($log->action === 'modification')
                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Modification</span>
                        @elseif($log->action === 'suppression')
                            <span class="bg-red-50 text-red-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Suppression</span>
                        @elseif($log->action === 'annulation')
                            <span class="bg-orange-50 text-orange-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">Annulation</span>
                        @else
                            <span class="bg-gray-50 text-gray-500 px-3 py-1 rounded-lg text-[10px] font-black uppercase">{{ $log->action }}</span>
                        @endif
                    </td>
                    <td class="p-5 text-xs text-gray-600 max-w-[300px]">{{ $log->description }}</td>
                    <td class="p-5">
                        @if($log->model_type)
                            <span class="bg-indigo-50 text-indigo-500 px-2 py-1 rounded text-[9px] font-black uppercase">{{ $log->model_type }} #{{ $log->model_id }}</span>
                        @endif
                    </td>
                    <td class="p-5 text-[10px] text-gray-400 font-mono">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-20 text-center">
                        <i class="fas fa-clipboard-list text-4xl text-gray-200 mb-3"></i>
                        <p class="text-gray-400 italic text-xs font-bold uppercase">Aucune activité enregistrée</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="p-4 bg-gray-50/50 border-t border-gray-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
