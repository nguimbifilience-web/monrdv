<?php

namespace App\Http\Controllers;

use App\Models\Specialite;
use Illuminate\Http\Request;

class SpecialiteController extends Controller
{
    public function index(Request $request) {
        $search = $request->get('search');
        $query = Specialite::query();

        if ($search) { $query->where('nom', 'like', "%{$search}%"); }

        $specialites = $query->orderBy('nom', 'asc')->get();
        $total = $specialites->count();

        return view('specialites.index', compact('specialites', 'total'));
    }

    public function store(Request $request) {
        $data = $request->validate(['nom' => 'required|unique:specialites']);
        Specialite::create($data);
        return back()->with('success', 'Créé.');
    }

    public function update(Request $request, $id) {
        $spec = Specialite::findOrFail($id);
        $data = $request->validate(['nom' => 'required|unique:specialites,nom,'.$id]);
        $spec->update($data);
        return back()->with('success', 'Modifié.');
    }

    public function destroy($id) {
        Specialite::destroy($id);
        return back()->with('error', 'Supprimé.');
    }
}