@extends('layouts.master')

@section('content')
<div class="p-8">
    {{-- EN-TÊTE --}}
    <div class="flex justify-between items-center mb-10">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-black text-blue-900 uppercase italic">Assurances</h1>
                <span class="bg-blue-100 text-blue-900 px-3 py-1 rounded-full text-[10px] font-black border border-blue-200 uppercase">
                    {{ $total }} Partenaires
                </span>
            </div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Gestion des contacts et taux</p>
        </div>
        <button onclick="toggleModal('modalAdd')" class="bg-cyan-400 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase shadow-lg hover:scale-105 transition-all">
            + Nouveau Partenaire
        </button>
    </div>

    {{-- TABLEAU --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 overflow-hidden mb-8">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50 border-b border-gray-50">
                <tr class="text-[9px] font-black uppercase text-gray-300">
                    <th class="p-6">Partenaire</th>
                    <th class="p-6">Type</th>
                    <th class="p-6">Référent</th>
                    <th class="p-6">Contact</th>
                    <th class="p-6 text-center">Taux</th>
                    <th class="p-6 text-center">Patients</th>
                    <th class="p-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($assurances as $a)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="p-6 font-black text-blue-900 text-xs uppercase">{{ $a->nom }}</td>
                    <td class="p-6">
                        @if($a->type === 'publique')
                            <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-lg font-black text-[10px] uppercase">Publique</span>
                        @else
                            <span class="bg-violet-50 text-violet-600 px-3 py-1 rounded-lg font-black text-[10px] uppercase">Privée</span>
                        @endif
                    </td>
                    <td class="p-6 text-xs text-gray-500 font-bold">{{ $a->nom_referent }}</td>
                    <td class="p-6">
                        <div class="text-[10px] font-black text-blue-900">{{ $a->telephone }}</div>
                        <div class="text-[9px] text-gray-400 font-medium lowercase">{{ $a->email }}</div>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-cyan-50 text-cyan-600 px-3 py-1 rounded-lg font-black text-[10px]">{{ $a->taux_couverture }}%</span>
                    </td>
                    <td class="p-6 text-center">
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-black text-[11px]">{{ $a->patients_count }}</span>
                    </td>
                    <td class="p-6 text-right space-x-3 text-gray-300">
                        <button onclick='openEditModal(@json($a))' class="hover:text-cyan-400"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('assurances.destroy', $a->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Supprimer ?')" class="hover:text-red-400"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-20 text-center opacity-30 font-black italic text-xs uppercase">Aucun partenaire</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- GRAPHIQUE D'ÉVOLUTION --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-50 p-8">
        <h2 class="text-sm font-black text-blue-900 uppercase italic mb-6">
            <i class="fas fa-chart-line text-cyan-400 mr-2"></i>
            Évolution des patients par assurance (6 derniers mois)
        </h2>
        <div style="height: 300px;">
            <canvas id="assuranceChart"></canvas>
        </div>
    </div>
</div>

{{-- MODAL AJOUT --}}
<div id="modalAdd" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-8 shadow-2xl">
        <form action="{{ route('assurances.store') }}" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            <h2 class="col-span-2 text-xl font-black text-blue-900 uppercase italic mb-4">Nouveau Partenaire</h2>
            <input type="text" name="nom" placeholder="Nom de l'assurance" class="col-span-2 bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <select name="type" class="bg-gray-50 border-none rounded-xl p-4 font-bold text-sm" required>
                <option value="">Type...</option>
                <option value="publique">Publique</option>
                <option value="privée">Privée</option>
            </select>
            <input type="number" name="taux_couverture" placeholder="Taux de couverture %" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="text" name="nom_referent" placeholder="Nom du Référent" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="text" name="telephone" placeholder="Téléphone" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="email" name="email" placeholder="Adresse Email" class="col-span-2 bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <div class="col-span-2 flex justify-end gap-4 mt-4">
                <button type="button" onclick="toggleModal('modalAdd')" class="text-[10px] font-black uppercase text-gray-400">Annuler</button>
                <button type="submit" class="bg-cyan-400 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px]">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="hidden fixed inset-0 bg-blue-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-xl p-8 shadow-2xl">
        <form id="editForm" method="POST" class="grid grid-cols-2 gap-4">
            @csrf @method('PUT')
            <h2 class="col-span-2 text-xl font-black text-cyan-400 uppercase italic mb-4">Modifier les informations</h2>
            <input type="text" name="nom" id="enom" class="col-span-2 bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <select name="type" id="etype" class="bg-gray-50 border-none rounded-xl p-4 font-bold text-sm" required>
                <option value="publique">Publique</option>
                <option value="privée">Privée</option>
            </select>
            <input type="number" name="taux_couverture" id="etaux" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="text" name="nom_referent" id="eref" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="text" name="telephone" id="etel" class="bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <input type="email" name="email" id="email" class="col-span-2 bg-gray-50 border-none rounded-xl p-4 font-bold" required>
            <div class="col-span-2 flex justify-end gap-4 mt-4">
                <button type="button" onclick="toggleModal('modalEdit')" class="text-[10px] font-black uppercase text-gray-400">Fermer</button>
                <button type="submit" class="bg-blue-900 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px]">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function openEditModal(a) {
        document.getElementById('editForm').action = `/assurances/${a.id}`;
        document.getElementById('enom').value = a.nom;
        document.getElementById('etype').value = a.type || 'privée';
        document.getElementById('eref').value = a.nom_referent;
        document.getElementById('etaux').value = a.taux_couverture;
        document.getElementById('etel').value = a.telephone;
        document.getElementById('email').value = a.email;
        toggleModal('modalEdit');
    }

    // Graphique d'évolution
    const ctx = document.getElementById('assuranceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: @json($chartData),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 11, weight: 'bold' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { size: 10, weight: 'bold' },
                        color: '#94a3b8'
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: {
                        font: { size: 10, weight: 'bold' },
                        color: '#94a3b8'
                    },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
