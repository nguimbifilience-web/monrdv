<div class="w-64 bg-blue-900 h-screen flex flex-col shadow-2xl">
    <div class="p-8">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                <span class="text-white font-black text-sm italic tracking-tighter">LZy</span>
            </div>
            <div>
                <h1 class="text-white font-black text-2xl uppercase tracking-tighter italic">
                    Mon<span class="text-orange-500">RDV</span>
                </h1>
                <p class="text-blue-400 text-[10px] uppercase font-bold tracking-widest">
                    Gestion Médicale
                </p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <!-- Dashboard (tous) -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Dashboard</span>
        </a>

        <!-- Patients (Admin + Secrétaire) -->
        <a href="{{ route('patients.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patients.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Patients</span>
        </a>

        <!-- Rendez-vous (Admin + Secrétaire) -->
        <a href="{{ route('rendezvous.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('rendezvous.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Rendez-vous</span>
        </a>

        <!-- Planning (Admin + Secrétaire) -->
        <a href="{{ route('medecins.schedule') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.schedule') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Planning</span>
        </a>

        <!-- Consultations (Admin + Secrétaire) -->
        <a href="{{ route('consultations.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('consultations.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-history"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Historique</span>
        </a>

        @if(auth()->user()->isAdmin())

        <!-- Médecins (Admin uniquement) -->
        <a href="{{ route('medecins.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.*') && !request()->routeIs('medecins.schedule') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-md"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Médecins</span>
        </a>

        <!-- Assurances (Admin uniquement) -->
        <a href="{{ route('assurances.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('assurances.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-shield-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Assurances</span>
        </a>

        <!-- Spécialités (Admin uniquement) -->
        <a href="{{ route('specialites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('specialites.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-stethoscope"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Spécialités</span>
        </a>

        <!-- Comptes (Admin uniquement) -->
        <a href="{{ route('comptes.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('comptes.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-users-cog"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Comptes</span>
        </a>

        <!-- Traçabilité (Admin uniquement) -->
        <a href="{{ route('activites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('activites.*') ? 'bg-orange-500 text-white' : 'text-blue-200 hover:bg-white/10' }} transition-all">
            <i class="fas fa-clipboard-list"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Traçabilité</span>
        </a>

        @endif

    </nav>

    {{-- Codes de validation en attente --}}
    @php
        $pendingCodes = \App\Models\PatientValidationCode::with('requestedBy')
            ->pending()
            ->latest()
            ->get();
    @endphp
    @if($pendingCodes->count() > 0)
    <div class="px-4 pb-3">
        <div class="bg-orange-500/20 border border-orange-500/30 rounded-2xl p-4">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 bg-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-white text-[10px]"></i>
                </div>
                <span class="text-orange-300 text-[10px] font-black uppercase tracking-widest">Codes en attente</span>
                <span class="ml-auto bg-orange-500 text-white text-[9px] font-black rounded-full w-5 h-5 flex items-center justify-center">{{ $pendingCodes->count() }}</span>
            </div>
            @foreach($pendingCodes as $pc)
            <div class="bg-white/10 rounded-xl p-3 mb-2 last:mb-0">
                <p class="text-white text-[10px] font-bold">
                    <i class="fas fa-user-plus mr-1 text-orange-400"></i>
                    {{ $pc->patient_prenom }} {{ $pc->patient_nom }}
                </p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-orange-300 text-lg font-black tracking-[0.3em] font-mono">{{ $pc->code }}</span>
                    <span class="text-blue-400 text-[9px]">
                        <i class="fas fa-clock mr-1"></i>{{ $pc->expires_at->diffForHumans() }}
                    </span>
                </div>
                <p class="text-blue-400 text-[9px] mt-1">
                    Par : {{ $pc->requestedBy->name }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="p-8 border-t border-white/10">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 {{ auth()->user()->isAdmin() ? 'bg-orange-500' : 'bg-blue-500' }} rounded-lg flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-blue-400 text-[9px] italic">{{ auth()->user()->isAdmin() ? 'Administrateur' : 'Secrétaire' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-400 hover:text-red-400 transition-colors" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
