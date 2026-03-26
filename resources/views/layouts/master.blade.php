<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MonRDV - Système de Gestion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc] flex h-screen overflow-hidden font-sans">

    @include('layouts.sidebar')

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-end px-10 shrink-0">
            <span class="text-[10px] font-black text-blue-900/40 uppercase tracking-widest">Admin MonRDV</span>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if(modal) modal.classList.toggle('hidden');
        }
    </script>
</body>
</html>