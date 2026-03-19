@extends('layouts.app') @section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Ajouter une nouvelle Spécialité</h1>

    <form action="{{ route('specialites.store') }}" method="POST" class="bg-white p-6 rounded shadow-md">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700">Nom de la spécialité</label>
            <input type="text" name="nom" class="w-full border rounded p-2" placeholder="ex: Cardiologie" required>
            @error('nom') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Description (Optionnel)</label>
            <textarea name="description" class="w-full border rounded p-2" placeholder="Brève description du service..."></textarea>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Enregistrer la spécialité
        </button>
        <a href="{{ route('specialites.index') }}" class="ml-4 text-gray-600">Annuler</a>
    </form>
</div>
@endsection