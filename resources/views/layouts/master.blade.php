<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonRDV - Système de Gestion</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#f8fafc] flex h-screen overflow-hidden font-sans">

    @if(auth()->user()->isPatient())
        @include('layouts.sidebar-patient')
    @elseif(auth()->user()->isMedecin())
        @include('layouts.sidebar-medecin')
    @else
        @include('layouts.sidebar')
    @endif

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-end px-10 shrink-0">
            @php
                $roleLabel = match(auth()->user()->role) {
                    'admin' => 'Admin',
                    'secretaire' => 'Secrétaire',
                    'medecin' => 'Médecin',
                    'patient' => 'Patient',
                    default => 'Utilisateur',
                };
            @endphp
            <span class="text-[10px] font-black text-blue-900/40 uppercase tracking-widest">{{ $roleLabel }} MonRDV</span>
        </header>

        <main class="flex-1 overflow-y-auto p-8" id="main-content">
            @yield('content')
        </main>
    </div>

    <script>
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
