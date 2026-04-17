<div class="w-72 md:w-64 h-screen flex flex-col shadow-2xl overflow-y-auto" style="background-color: var(--clinic-primary)">
    <div class="p-6 md:p-8 flex items-center justify-between">
        <div class="flex items-center gap-3 min-w-0">
            @if($clinicLogoUrl ?? false)
                <img src="{{ $clinicLogoUrl }}" alt="{{ $clinicName }}" class="w-10 h-10 rounded-xl object-cover shadow-lg shrink-0">
            @else
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shrink-0" style="background-color: var(--clinic-secondary)">
                    <span class="text-white font-black text-sm italic tracking-tighter">{{ strtoupper(substr($clinicName ?? 'M', 0, 2)) }}</span>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-white font-black text-xl md:text-2xl uppercase tracking-tighter italic truncate">
                    {{ $clinicName ?? 'MonRDV' }}
                </h1>
                <p class="text-green-400 text-[10px] uppercase font-bold tracking-widest">Espace Medecin</p>
            </div>
        </div>
        <button type="button" onclick="closeSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 text-white shrink-0"><i class="fas fa-times"></i></button>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <a href="{{ route('medecin.dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.dashboard') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Accueil</span>
        </a>

        <a href="{{ route('medecin.planning') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.planning') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-alt"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mon Planning</span>
        </a>

        <a href="{{ route('medecin.rendezvous') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.rendezvous') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Rendez-vous</span>
        </a>

        <a href="{{ route('medecin.patients') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.patients') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-user-injured"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Patients</span>
        </a>

        <a href="{{ route('medecin.ordonnances.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.ordonnances.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-prescription"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Ordonnances</span>
        </a>

        <a href="{{ route('medecin.examens.index') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('medecin.examens.*') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-vials"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Feuilles d'examen</span>
        </a>

    </nav>

    <div class="p-8 border-t border-white/10">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-white/40 hover:text-white/80 transition-colors mb-4 text-[10px] font-bold uppercase tracking-widest">
            <i class="fas fa-user-cog"></i> Mon Profil
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-blue-400 text-[9px] italic">Medecin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-400 hover:text-red-400 transition-colors" title="Deconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
