<div class="w-72 md:w-64 h-screen flex flex-col shadow-2xl overflow-y-auto" style="background-color: var(--clinic-primary)">
    <div class="p-6 md:p-8 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            @if($clinicLogoUrl ?? false)
                <img src="{{ $clinicLogoUrl }}" alt="{{ $clinicName }}" class="w-10 h-10 rounded-xl object-cover shadow-lg shrink-0">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shrink-0" style="background-color: var(--clinic-secondary)">
                    <span class="font-black text-sm italic tracking-tighter" style="color: var(--clinic-sidebar-text)">{{ strtoupper(substr($clinicName ?? 'M', 0, 2)) }}</span>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="font-black text-xl md:text-2xl uppercase tracking-tighter italic truncate" style="color: var(--clinic-sidebar-text)">
                    {{ $clinicName ?? 'MonRDV' }}
                </h1>
                <p class="text-[10px] uppercase font-bold tracking-widest" style="color: var(--clinic-sidebar-text); opacity: 0.5;">
                    Gestion Médicale
                </p>
            </div>
        </div>
        <button type="button" onclick="closeSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 shrink-0" style="color: var(--clinic-sidebar-text)">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <!-- Dashboard (tous) -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('dashboard') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('dashboard')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Dashboard</span>
        </a>

        <!-- Patients (Admin + Secrétaire) -->
        <a href="{{ route('patients.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patients.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('patients.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Patients</span>
        </a>

        <!-- Rendez-vous (Admin + Secrétaire) -->
        <a href="{{ route('rendezvous.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('rendezvous.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('rendezvous.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Rendez-vous</span>
        </a>

        <!-- Planning (Admin + Secrétaire) -->
        <a href="{{ route('medecins.schedule') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.schedule') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('medecins.schedule')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-calendar-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Planning</span>
        </a>

        <!-- Consultations (Admin + Secrétaire) -->
        <a href="{{ route('consultations.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('consultations.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('consultations.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-history"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Historique</span>
        </a>

        @if(auth()->user()->isAdmin())

        <!-- Médecins (Admin uniquement) -->
        <a href="{{ route('medecins.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecins.*') && !request()->routeIs('medecins.schedule') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('medecins.*') && !request()->routeIs('medecins.schedule')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-user-md"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Médecins</span>
        </a>

        <!-- Assurances (Admin uniquement) -->
        <a href="{{ route('assurances.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('assurances.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('assurances.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-shield-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Assurances</span>
        </a>

        <!-- Spécialités (Admin uniquement) -->
        <a href="{{ route('specialites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('specialites.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('specialites.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-stethoscope"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Spécialités</span>
        </a>

        <!-- Comptes (Admin uniquement) -->
        <a href="{{ route('comptes.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('comptes.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('comptes.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-users-cog"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Comptes</span>
        </a>

        <!-- Traçabilité (Admin uniquement) -->
        <a href="{{ route('activites.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('activites.*') ? 'nav-active' : 'hover:bg-white/10' }} transition-all"
           @unless(request()->routeIs('activites.*')) style="color: var(--clinic-sidebar-text); opacity: 0.6;" @endunless>
            <i class="fas fa-clipboard-list"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Traçabilité</span>
        </a>

        @endif

    </nav>

    <div class="p-6 md:p-8 border-t shrink-0" style="border-color: rgba(255,255,255,0.1);">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 hover:opacity-80 transition-colors mb-4 text-[10px] font-bold uppercase tracking-widest" style="color: var(--clinic-sidebar-text); opacity: 0.4;">
            <i class="fas fa-user-cog"></i> Mon Profil
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black" style="background-color: var(--clinic-secondary); color: var(--clinic-sidebar-text)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-tight" style="color: var(--clinic-sidebar-text)">{{ auth()->user()->name }}</p>
                    <p class="text-[9px] italic" style="color: var(--clinic-sidebar-text); opacity: 0.4;">{{ auth()->user()->isAdmin() ? 'Administrateur' : 'Secrétaire' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="hover:text-red-400 transition-colors" style="color: var(--clinic-sidebar-text); opacity: 0.4;" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
