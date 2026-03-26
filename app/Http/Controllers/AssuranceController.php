<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use Illuminate\Http\Request;

class AssuranceController extends Controller
{
    public function index(Request $request) {
        $search = $request->get('search');
        $query = Assurance::query();

        if ($search) {
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('nom_referent', 'like', "%{$search}%");
        }

        $assurances = $query->orderBy('created_at', 'desc')->get();
        $total = $assurances->count();

        return view('assurances.index', compact('assurances', 'total'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nom' => 'required|string',
            'nom_referent' => 'required|string',
            'taux_couverture' => 'required|numeric',
            'telephone' => 'required|string', // Ajouté
            'email' => 'required|email',       // Ajouté
        ]);
        Assurance::create($data);
        return back()->with('success', 'Partenaire ajouté avec ses contacts.');
    }

    public function update(Request $request, $id) {
        $assurance = Assurance::findOrFail($id);
        $data = $request->validate([
            'nom' => 'required|string',
            'nom_referent' => 'required|string',
            'taux_couverture' => 'required|numeric',
            'telephone' => 'required|string', // Ajouté
            'email' => 'required|email',       // Ajouté
        ]);
        $assurance->update($data);
        return back()->with('success', 'Contacts mis à jour.');
    }

    public function destroy($id) {
        Assurance::destroy($id);
        return back()->with('error', 'Partenaire supprimé.');
    }
}