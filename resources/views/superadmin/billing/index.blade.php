@extends('layouts.master')

@section('content')
<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-black text-blue-900 uppercase italic">Facturation</h1>
        <p class="text-[10px] md:text-xs text-gray-400 font-bold uppercase tracking-widest">Abonnements des cliniques</p>
    </div>
    <a href="{{ route('superadmin.billing.plans') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-5 md:px-6 py-3 rounded-2xl shadow-lg uppercase tracking-widest text-[10px]">
        <i class="fas fa-gem mr-2"></i>Gérer les plans
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl" id="flash-msg">
        <p class="text-xs font-bold">{{ session('success') }}</p>
    </div>
@endif

{{-- Filtres --}}
<form method="GET" action="{{ route('superadmin.billing.index') }}" class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-50 p-4 md:p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="relative sm:col-span-2">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une clinique..." class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl pl-10 pr-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
        </div>
        <select name="plan" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Tous les plans</option>
            @foreach($plans as $p)
                <option value="{{ $p->slug }}" @selected(request('plan') === $p->slug)>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="status" class="bg-gray-50 border-2 border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-blue-900 focus:border-blue-500 focus:ring-0">
            <option value="">Tous les statuts</option>
            <option value="active" @selected(request('status') === 'active')>À jour</option>
            <option value="expiring" @selected(request('status') === 'expiring')>Expire bientôt</option>
            <option value="expired" @selected(request('status') === 'expired')>Expiré</option>
        </select>
    </div>
    <button type="submit" class="sr-only">Filtrer</button>
</form>

<div class="bg-white rounded-2xl md:rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left min-w-[720px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-[9px] font-black uppercase text-gray-500">
                    <th class="p-5">Clinique</th>
                    <th class="p-5">Plan</th>
                    <th class="p-5 text-right">Prix/mois</th>
                    <th class="p-5">Début</th>
                    <th class="p-5">Expiration</th>
                    <th class="p-5 text-center">Statut</th>
                    <th class="p-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($clinics as $clinic)
                    @php
                        $st = $clinic->subscription_status;
                        $badge = match($st) {
                            'active' => ['bg-green-100 text-green-600', 'À jour'],
                            'expiring' => ['bg-yellow-100 text-yellow-600', 'Expire bientôt'],
                            'expired' => ['bg-red-100 text-red-600', 'Expiré'],
                            default => ['bg-gray-100 text-gray-500', 'Aucun'],
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="p-5 text-xs font-bold text-blue-900">{{ $clinic->name }}</td>
                        <td class="p-5 text-xs font-bold text-indigo-600">{{ $clinic->plan?->name ?? '—' }}</td>
                        <td class="p-5 text-xs text-right font-black text-gray-700">{{ $clinic->plan ? number_format($clinic->plan->price_monthly, 0, ',', ' ') : '—' }}</td>
                        <td class="p-5 text-xs text-gray-500">{{ $clinic->subscription_started_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-5 text-xs text-gray-500">{{ $clinic->subscription_expires_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-5 text-center"><span class="{{ $badge[0] }} px-3 py-1 rounded-full text-[9px] font-black uppercase">{{ $badge[1] }}</span></td>
                        <td class="p-5 text-center">
                            <button type="button" data-billing="{{ json_encode(['id'=>$clinic->id,'name'=>$clinic->name,'plan_id'=>$clinic->plan_id,'subscription_started_at'=>$clinic->subscription_started_at instanceof \DateTimeInterface ? $clinic->subscription_started_at->format('Y-m-d') : null,'subscription_expires_at'=>$clinic->subscription_expires_at instanceof \DateTimeInterface ? $clinic->subscription_expires_at->format('Y-m-d') : null], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_INVALID_UTF8_SUBSTITUTE) }}" onclick="editBilling(JSON.parse(this.dataset.billing))"
                                    class="w-8 h-8 bg-orange-50 text-orange-500 rounded-lg inline-flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all" title="Modifier">
                                <i class="fas fa-pen text-[10px]"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-16 text-center text-gray-400 text-xs italic">Aucune clinique</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<div class="mt-4">{{ $clinics->links() }}</div>

{{-- Modal Edit billing --}}
<div id="modalEditBilling" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl md:rounded-3xl w-full max-w-md shadow-2xl">
        <div class="bg-orange-500 py-4 px-6 rounded-t-2xl md:rounded-t-3xl flex items-center justify-between">
            <h3 class="text-white font-black text-base uppercase tracking-widest">Abonnement</h3>
            <button type="button" onclick="toggleModal('modalEditBilling')" class="text-white/60 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="billingForm" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <p class="text-xs text-gray-500">Clinique : <span id="billing_name" class="font-black text-blue-900"></span></p>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Plan</label>
                <select name="plan_id" id="billing_plan" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                    <option value="">— Aucun —</option>
                    @foreach($plans as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price_monthly, 0, ',', ' ') }} XAF/mois)</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Début</label>
                    <input type="date" name="subscription_started_at" id="billing_start" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Expiration</label>
                    <input type="date" name="subscription_expires_at" id="billing_end" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 font-bold text-blue-900 text-sm focus:border-orange-500 focus:ring-0">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="toggleModal('modalEditBilling')" class="text-gray-400 text-[10px] font-black uppercase">Annuler</button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black px-6 py-3 rounded-xl text-xs uppercase"><i class="fas fa-save mr-2"></i>Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editBilling(c) {
    document.getElementById('billing_name').textContent = c.name;
    document.getElementById('billing_plan').value = c.plan_id || '';
    document.getElementById('billing_start').value = c.subscription_started_at || '';
    document.getElementById('billing_end').value = c.subscription_expires_at || '';
    document.getElementById('billingForm').action = '/super-admin/facturation/' + c.id;
    toggleModal('modalEditBilling');
}
</script>
@endsection
