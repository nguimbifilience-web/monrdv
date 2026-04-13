<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $clinicName ?? 'MonRDV' }} - Gestion</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.clinic-branding-css')
    @php $theme = \App\Models\Setting::theme(); @endphp
    <style>
        :root {
            --mr-bg-page: {{ $theme['theme_bg_page'] }};
            --mr-bg-card: {{ $theme['theme_bg_card'] }};
            --mr-text-primary: {{ $theme['theme_text_primary'] }};
            --mr-accent: {{ $theme['theme_accent'] }};
            --mr-sidebar-bg: {{ $theme['theme_sidebar_bg'] }};
        }
        body.mr-themed { background-color: var(--mr-bg-page); }
        .mr-card { background-color: var(--mr-bg-card); }
        /* Sidebar drawer mobile */
        .sidebar-drawer {
            transition: transform 0.25s ease-in-out;
        }
        @media (max-width: 767px) {
            .sidebar-drawer {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 50;
                transform: translateX(-100%);
                height: 100vh;
            }
            .sidebar-drawer.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="mr-themed md:flex md:h-screen md:overflow-hidden font-sans">

    {{-- Backdrop mobile --}}
    <div id="sidebar-backdrop" onclick="closeSidebar()"
         class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"></div>

    {{-- Sidebar (wrapper responsive) --}}
    <div id="sidebar" class="sidebar-drawer shrink-0">
        @if(auth()->user()->isSuperAdmin())
            @include('layouts.sidebar-superadmin')
        @elseif(auth()->user()->isPatient())
            @include('layouts.sidebar-patient')
        @elseif(auth()->user()->isMedecin())
            @include('layouts.sidebar-medecin')
        @else
            @include('layouts.sidebar')
        @endif
    </div>

    <div class="flex-1 flex flex-col min-w-0 md:h-screen">
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 md:px-10 shrink-0 sticky top-0 z-30">
            {{-- Burger mobile --}}
            <button type="button" onclick="openSidebar()"
                    class="md:hidden w-10 h-10 flex items-center justify-center text-blue-900 hover:bg-blue-50 rounded-xl transition-colors"
                    aria-label="Ouvrir le menu">
                <i class="fas fa-bars text-lg"></i>
            </button>

            {{-- Logo mobile (visible uniquement sur mobile) --}}
            <div class="md:hidden flex items-center gap-2">
                <span class="font-black text-blue-900 text-sm uppercase italic tracking-tighter">
                    Mon<span class="text-orange-500">RDV</span>
                </span>
            </div>

            {{-- Infos user (desktop) --}}
            @php
                $roleLabel = match(auth()->user()->role) {
                    'super_admin' => 'Super Admin',
                    'admin' => 'Admin',
                    'secretaire' => 'Secrétaire',
                    'medecin' => 'Médecin',
                    'patient' => 'Patient',
                    default => 'Utilisateur',
                };
            @endphp
            <span class="hidden md:inline text-[10px] font-black text-blue-900/40 uppercase tracking-widest">
                {{ $roleLabel }} — {{ $clinicName ?? 'MonRDV' }}
            </span>

            {{-- Spacer mobile --}}
            <div class="md:hidden w-10"></div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8" id="main-content">
            @yield('content')
        </main>
    </div>

    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebar-backdrop').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-backdrop').classList.add('hidden');
            document.body.style.overflow = '';
        }
        // Fermer le drawer en passant en desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeSidebar();
        });

        function toggleModal(id) {
            const modal = document.getElementById(id);
            if(modal) modal.classList.toggle('hidden');
        }

        // Flash messages auto-remove
        const flash = document.getElementById('flash-msg');
        if (flash) setTimeout(() => flash.remove(), 3000);
    </script>
    @stack('scripts')
</body>
</html>
