<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Consultation #{{ str_pad($consultation->id, 5, '0', STR_PAD_LEFT) }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .ticket { box-shadow: none !important; border: 1px solid #ddd !important; }
        }
        @page {
            size: 80mm auto;
            margin: 5mm;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-8">

    {{-- Boutons --}}
    <div class="no-print fixed top-6 right-6 flex gap-3 z-50">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase shadow-lg hover:bg-blue-700 transition-all">
            <i class="fas fa-print mr-2"></i> Imprimer
        </button>
        <a href="{{ route('rendezvous.index') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-black text-xs uppercase hover:bg-gray-300 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Retour
        </a>
    </div>

    <div class="ticket bg-white rounded-2xl shadow-xl w-full max-w-sm p-8">
        {{-- En-tête --}}
        @php $ticketClinic = $consultation->patient->clinic ?? auth()->user()->clinic; @endphp
        <div class="text-center border-b-2 border-dashed border-gray-200 pb-6 mb-6">
            @if($ticketClinic?->logo_url)
                <img src="{{ $ticketClinic->logo_url }}" alt="{{ $ticketClinic->name }}" class="w-16 h-16 mx-auto rounded-xl object-cover mb-2">
            @endif
            <h1 class="text-2xl font-black text-blue-900 uppercase">{{ $ticketClinic?->name ?? 'MonRDV' }}</h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $ticketClinic?->address ?? 'Système de gestion médicale' }}</p>
            <div class="mt-3">
                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase border border-green-100">
                    Ticket de Consultation
                </span>
            </div>
        </div>

        {{-- Numéro et date --}}
        <div class="flex justify-between items-center mb-6">
            <span class="text-[10px] font-black text-gray-400 uppercase">N° {{ str_pad($consultation->id, 5, '0', STR_PAD_LEFT) }}</span>
            <span class="text-[10px] font-black text-gray-400">{{ $consultation->created_at->format('d/m/Y à H:i') }}</span>
        </div>

        {{-- Patient --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Patient</span>
            <p class="font-black text-blue-900 text-sm uppercase">{{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}</p>
            <p class="text-[10px] text-gray-500">{{ $consultation->patient->telephone }}</p>
        </div>

        {{-- Médecin --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-4">
            <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Médecin</span>
            <p class="font-bold text-gray-700 text-sm">Dr. {{ $consultation->medecin->nom }} {{ $consultation->medecin->prenom }}</p>
            <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-[9px] font-black uppercase">
                {{ $consultation->medecin->specialite->nom ?? 'Généraliste' }}
            </span>
        </div>

        {{-- Assurance --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Assurance</span>
            @if($consultation->taux_couverture > 0 && $consultation->patient->assurance)
                <p class="font-black text-green-600 text-sm">{{ $consultation->patient->assurance->nom }}</p>
                <span class="text-[10px] font-bold text-green-500">Taux de couverture : {{ $consultation->taux_couverture }}%</span>
            @else
                <p class="font-black text-red-400 text-sm">Non assuré</p>
            @endif
        </div>

        {{-- Détails financiers --}}
        <div class="border-t-2 border-dashed border-gray-200 pt-4 space-y-3">
            <div class="flex justify-between text-xs">
                <span class="font-bold text-gray-500">Montant consultation</span>
                <span class="font-black text-blue-900">{{ number_format($consultation->montant_total, 0, ',', ' ') }} FCFA</span>
            </div>

            @if($consultation->taux_couverture > 0)
            <div class="flex justify-between text-xs">
                <span class="font-bold text-green-600">Part assurance ({{ $consultation->taux_couverture }}%)</span>
                <span class="font-black text-green-600">- {{ number_format($consultation->montant_assurance, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif

            <div class="flex justify-between text-sm border-t border-gray-200 pt-3">
                <span class="font-black text-blue-900 uppercase">A payer</span>
                <span class="font-black text-blue-900 text-lg">{{ number_format($consultation->montant_patient, 0, ',', ' ') }} FCFA</span>
            </div>

            <div class="flex justify-between text-xs bg-blue-50 rounded-xl p-3">
                <span class="font-bold text-blue-600">Montant donné</span>
                <span class="font-black text-blue-600">{{ number_format($consultation->montant_donne, 0, ',', ' ') }} FCFA</span>
            </div>

            @if($consultation->montant_rendu > 0)
            <div class="flex justify-between text-sm bg-orange-50 rounded-xl p-3 border border-orange-100">
                <span class="font-black text-orange-600 uppercase">Monnaie rendue</span>
                <span class="font-black text-orange-600 text-lg">{{ number_format($consultation->montant_rendu, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
        </div>

        {{-- Notes --}}
        @if($consultation->notes)
        <div class="mt-4 bg-gray-50 rounded-xl p-3">
            <span class="text-[9px] font-black text-gray-400 uppercase block mb-1">Notes</span>
            <p class="text-xs text-gray-600">{{ $consultation->notes }}</p>
        </div>
        @endif

        {{-- Pied de ticket --}}
        <div class="mt-6 pt-4 border-t-2 border-dashed border-gray-200 text-center">
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Merci pour votre confiance</p>
            <p class="text-[8px] text-gray-300 mt-1">{{ $ticketClinic?->name ?? 'MonRDV' }} - Gestion médicale</p>
        </div>
    </div>

</body>
</html>
