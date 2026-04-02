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
                <p class="text-cyan-400 text-[10px] uppercase font-bold tracking-widest">Espace Patient</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2">

        <a href="{{ route('patient.dashboard') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patient.dashboard') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-th-large"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Accueil</span>
        </a>

        <a href="{{ route('patient.prendre-rdv') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patient.prendre-rdv') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-plus"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Prendre RDV</span>
        </a>

        <a href="{{ route('patient.rendezvous') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patient.rendezvous') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-calendar-check"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Rendez-vous</span>
        </a>

        <a href="{{ route('patient.documents') }}"
           class="flex items-center gap-4 px-6 py-4 rounded-2xl
           {{ request()->routeIs('patient.documents') ? 'nav-active' : 'text-white/60 hover:bg-white/10' }} transition-all">
            <i class="fas fa-folder-open"></i>
            <span class="font-bold text-sm uppercase tracking-widest text-[10px]">Mes Documents</span>
        </a>

    </nav>

    <div class="p-8 border-t border-white/10">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-white/40 hover:text-white/80 transition-colors mb-4 text-[10px] font-bold uppercase tracking-widest">
            <i class="fas fa-user-cog"></i> Mon Profil
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center text-white text-xs font-black">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-white text-[10px] font-black uppercase tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-blue-400 text-[9px] italic">Patient</p>
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
