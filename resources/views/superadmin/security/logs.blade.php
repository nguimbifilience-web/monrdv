@extends('layouts.master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Logs d'activité</h1>
    <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Audit global — toutes cliniques confondues</p>
</div>

{{-- Filtres --}}
<form method="GET" action="{{ route('superadmin.security.logs') }}" class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-4 md:p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <select name="clinic_id" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les cliniques</option>
            @foreach($clinics as $c)
                <option value="{{ $c->id }}" @selected(request('clinic_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="action" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Toutes les actions</option>
            @foreach($actions as $a)
                <option value="{{ $a }}" @selected(request('action') === $a)>{{ $a }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black py-3 rounded-xl text-xs uppercase tracking-widest"><i class="fas fa-filter mr-1"></i>Filtrer</button>
    </div>
</form>

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-400">
                    <th class="p-4">Date</th>
                    <th class="p-4">Utilisateur</th>
                    <th class="p-4">Clinique</th>
                    <th class="p-4">Action</th>
                    <th class="p-4">Cible</th>
                    <th class="p-4">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-4 text-[10px] text-gray-500 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-4 text-xs font-bold text-blue-900">{{ $log->user?->name ?? '—' }}</td>
                        <td class="p-4 text-xs text-gray-600">{{ $log->clinic?->name ?? '—' }}</td>
                        <td class="p-4"><span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[9px] font-black uppercase">{{ $log->action }}</span></td>
                        <td class="p-4 text-xs text-gray-500 truncate max-w-xs">{{ $log->description ?? ($log->model_type . ' #' . $log->model_id) }}</td>
                        <td class="p-4 text-[10px] text-gray-400 font-mono">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-16 text-center text-gray-400 text-xs italic">Aucun log</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-50">
        @forelse($logs as $log)
            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-xs font-black text-blue-900 truncate">{{ $log->user?->name ?? '—' }}</p>
                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[9px] font-black uppercase shrink-0">{{ $log->action }}</span>
                </div>
                <p class="text-[10px] text-gray-500 truncate">{{ $log->description ?? ($log->model_type . ' #' . $log->model_id) }}</p>
                <p class="text-[9px] text-gray-400 mt-1">
                    {{ $log->clinic?->name ?? '—' }} — {{ $log->created_at?->format('d/m/Y H:i') }}
                </p>
            </div>
        @empty
            <div class="p-12 text-center text-gray-400 text-xs italic">Aucun log</div>
        @endforelse
    </div>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
