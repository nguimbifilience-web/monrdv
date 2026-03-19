<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prendre RDV - MonRDV</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-green-600">Nouveau Rendez-vous</h1>
        
        <form action="/rendez-vous" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2">Patient</label>
                <select name="patient_id" class="w-full border p-2 rounded">
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2">Médecin</label>
                <select name="medecin_id" class="w-full border p-2 rounded">
                    @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }} ({{ $medecin->specialite->nom }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2">Date et Heure</label>
                <input type="datetime-local" name="date_rdv" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block mb-2">Motif</label>
                <textarea name="motif" class="w-full border p-2 rounded" rows="3"></textarea>
            </div>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Enregistrer le RDV
            </button>
        </form>
    </div>
</body>
</html>