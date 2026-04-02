<div class="w-64 h-screen flex flex-col shadow-2xl" style="background-color: var(--clinic-primary)">
    <div class="p-8">
        <div class="flex items-center gap-3">
            @if($clinicLogoUrl ?? false)
                <img src="{{ $clinicLogoUrl }}" alt="{{ $clinicName }}" class="w-10 h-10 rounded-xl object-cover shadow-lg">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg" style="background-color: var(--clinic-secondary)">
                    <span class="text-white font-black text-sm italic tracking-tighter">{{ strtoupper(substr($clinicName ?? 'M', 0, 2)) }}</span>
                </div>
            @endif
            <div>
                <h1 class="text-white font-black text-2xl uppercase tracking-tighter italic">
                    {{ $clinicName ?? 'MonRDV' }}
                </h1>
                <p class="text-white/50 text-[10px] uppercase font-bold tracking-widest">
                    Gestion Médicale
                </p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <!-- Dashboard (tous) -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('dashboard') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Dashboard</span>
        </a>

        <!-- Patients (Admin + Secrétaire) -->
        <a href="{{ route('patients.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patients.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Patients</span>
        </a>

        <!-- Rendez-vous (Admin + Secrétaire) -->
        <a href="{{ route('rendezvous.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('rendezvous.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Rendez-vous</span>
        </a>

        <!-- Planning (Admin + Secrétaire) -->
        <a href="{{ route('medecins.schedule') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.schedule') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Planning</span>
        </a>

        <!-- Consultations (Admin + Secrétaire) -->
        <a href="{{ route('consultations.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('consultations.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-history"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Historique</span>
        </a>

        @if(auth()->user()->isAdmin())

        <!-- Médecins (Admin uniquement) -->
        <a href="{{ route('medecins.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.*') && !request()->routeIs('medecins.schedule') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-md"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Médecins</span>
        </a>

        <!-- Assurances (Admin uniquement) -->
        <a href="{{ route('assurances.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('assurances.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-shield-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Assurances</span>
        </a>

        <!-- Spécialités (Admin uniquement) -->
        <a href="{{ route('specialites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('specialites.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-stethoscope"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Spécialités</span>
        </a>

        <!-- Comptes (Admin uniquement) -->
        <a href="{{ route('comptes.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('comptes.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-users-cog"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Comptes</span>
        </a>

        <!-- Traçabilité (Admin uniquement) -->
        <a href="{{ route('activites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('activites.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-clipboard-list"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Traçabilité</span>
        </a>

        @endif

    </nav>

    <div class="p-8 border-t border-white/10">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-white/40 hover:text-white/80 transition-colors mb-4 text-[10px] font-bold uppercase tracking-widest">
            <i class="fas fa-user-cog"></i> Mon Profil
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-black" style="background-color: var(--clinic-secondary)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-white/40 text-[9px] italic">{{ auth()->user()->isAdmin() ? 'Administrateur' : 'Secrétaire' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-white/40 hover:text-red-400 transition-colors" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
