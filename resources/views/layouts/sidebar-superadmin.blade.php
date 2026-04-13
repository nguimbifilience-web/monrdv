<div class="w-72 md:w-64 h-screen flex flex-col shadow-2xl overflow-y-auto" style="background-color: var(--mr-sidebar-bg)">
    <div class="p-6 md:p-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30">
                <span class="text-white font-black text-sm italic tracking-tighter">LZy</span>
            </div>
            <div>
                <h1 class="text-white font-black text-xl md:text-2xl uppercase tracking-tighter italic">
                    Mon<span class="text-red-500">RDV</span>
                </h1>
                <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                    Super Admin
                </p>
            </div>
        </div>
        {{-- Close button mobile --}}
        <button type="button" onclick="closeSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>

    @php
        $item = 'flex items-center gap-4 px-5 py-3 rounded-2xl transition-all';
        $active = 'bg-red-500 text-white shadow-lg shadow-red-500/30';
        $inactive = 'text-gray-300 hover:bg-white/10';
        $label = 'font-bold text-[10px] uppercase tracking-widest';

        $isDashboard = request()->routeIs('superadmin.dashboard') || request()->routeIs('dashboard');
        $isClinics = request()->routeIs('clinics.*');
        $isUsers = request()->routeIs('superadmin.users.*');
        $isBilling = request()->routeIs('superadmin.billing.*');
        $isSettings = request()->routeIs('superadmin.settings.*');
        $isSecurity = request()->routeIs('superadmin.security.*');
    @endphp

    <nav class="flex-1 px-4 space-y-1 pb-4">
        <a href="{{ route('superadmin.dashboard') }}" class="{{ $item }} {{ $isDashboard ? $active : $inactive }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span class="{{ $label }}">Dashboard</span>
        </a>
        <a href="{{ route('clinics.index') }}" class="{{ $item }} {{ $isClinics ? $active : $inactive }}">
            <i class="fas fa-hospital w-5 text-center"></i>
            <span class="{{ $label }}">Cliniques</span>
        </a>
        <a href="{{ route('superadmin.users.index') }}" class="{{ $item }} {{ $isUsers ? $active : $inactive }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span class="{{ $label }}">Utilisateurs</span>
        </a>
        <a href="{{ route('superadmin.billing.index') }}" class="{{ $item }} {{ $isBilling ? $active : $inactive }}">
            <i class="fas fa-credit-card w-5 text-center"></i>
            <span class="{{ $label }}">Facturation</span>
        </a>

        {{-- Paramètres globaux (groupe) --}}
        <div class="pt-3">
            <p class="px-5 text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Paramètres globaux</p>
            <a href="{{ route('superadmin.settings.specialites') }}" class="{{ $item }} {{ request()->routeIs('superadmin.settings.specialites*') ? $active : $inactive }}">
                <i class="fas fa-stethoscope w-5 text-center"></i>
                <span class="{{ $label }}">Spécialités</span>
            </a>
            <a href="{{ route('superadmin.settings.assurances') }}" class="{{ $item }} {{ request()->routeIs('superadmin.settings.assurances*') ? $active : $inactive }}">
                <i class="fas fa-shield-alt w-5 text-center"></i>
                <span class="{{ $label }}">Assurances</span>
            </a>
            <a href="{{ route('superadmin.settings.system') }}" class="{{ $item }} {{ request()->routeIs('superadmin.settings.system*') ? $active : $inactive }}">
                <i class="fas fa-sliders-h w-5 text-center"></i>
                <span class="{{ $label }}">Système</span>
            </a>
            <a href="{{ route('superadmin.settings.appearance') }}" class="{{ $item }} {{ request()->routeIs('superadmin.settings.appearance*') ? $active : $inactive }}">
                <i class="fas fa-palette w-5 text-center"></i>
                <span class="{{ $label }}">Apparence</span>
            </a>
        </div>

        {{-- Sécurité (groupe) --}}
        <div class="pt-3">
            <p class="px-5 text-[9px] font-black text-gray-500 uppercase tracking-widest mb-1">Sécurité</p>
            <a href="{{ route('superadmin.security.logs') }}" class="{{ $item }} {{ request()->routeIs('superadmin.security.logs') ? $active : $inactive }}">
                <i class="fas fa-history w-5 text-center"></i>
                <span class="{{ $label }}">Logs d'activité</span>
            </a>
            <a href="{{ route('superadmin.security.access') }}" class="{{ $item }} {{ request()->routeIs('superadmin.security.access') ? $active : $inactive }}">
                <i class="fas fa-user-lock w-5 text-center"></i>
                <span class="{{ $label }}">Gestion accès</span>
            </a>
        </div>
    </nav>

    <div class="p-6 border-t border-white/10 shrink-0">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 text-white/40 hover:text-white/80 transition-colors mb-4 text-[10px] font-bold uppercase tracking-widest">
            <i class="fas fa-user-cog"></i> Mon Profil
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white text-xs font-black shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-white text-[10px] font-black uppercase tracking-tight truncate">{{ auth()->user()->name }}</p>
                    <p class="text-gray-400 text-[9px] italic">Super Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</div>
