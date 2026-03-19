<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonRDV - Gestion Médicale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; }
        .nav-link { color: #d1d5db; text-decoration: none; padding: 10px 15px; border-radius: 5px; transition: 0.3s; }
        .nav-link:hover { background-color: #374151; color: white; }
        .active-link { background-color: #1f2937; color: #10b981; font-weight: bold; }
    </style>
</head>
<body>

    <nav style="background-color: #111827; padding: 15px 0; color: white; margin-bottom: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px;">
            <div style="font-size: 22px; font-weight: bold; color: #10b981;">
                🏥 Mon<span style="color: white;">RDV</span>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('rendezvous.index') }}" class="nav-link {{ request()->routeIs('rendezvous.*') ? 'active-link' : '' }}">
                    🗓️ Planning
                </a>
                <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active-link' : '' }}">
                    👥 Patients
                </a>
                <a href="{{ route('specialites.index') }}" class="nav-link {{ request()->routeIs('specialites.*') ? 'active-link' : '' }}">
                    🏥 Spécialités
                </a>
            </div>

            <div style="font-size: 12px; color: #9ca3af;">
                Admin: {{ $user_handle ?? 'nguimbifilience-web' }}
            </div>
        </div>
    </nav>

    <main style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #10b981;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background-color: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ef4444;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer style="text-align: center; margin-top: 50px; padding: 20px; color: #6b7280; font-size: 13px;">
        &copy; 2026 - Projet de Licence Système & Réseau - Application MonRDV
    </footer>

</body>
</html>