<?php

namespace App\Http\Controllers;

use App\Models\Specialite;
use App\Models\Assurance;
use App\Http\Requests\StoreSpecialiteRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SpecialiteController extends Controller
{
    public function index(Request $request) {
        $this->authorize('viewAny', Specialite::class);
        $search = $request->get('search');
        $query = Specialite::withCount(['medecins', 'rendezvous as rdv_mois_count' => function ($q) {
            $q->whereMonth('date_rv', Carbon::now()->month)
              ->whereYear('date_rv', Carbon::now()->year);
        }]);

        if ($search) { $query->where('nom', 'like', "%{$search}%"); }

        $specialites = $query->orderBy('nom', 'asc')->get();
        $total = $specialites->count();
        $assurances = Assurance::where('taux_couverture', '>', 0)->orderBy('nom')->get();

        return view('specialites.index', compact('specialites', 'total', 'assurances'));
    }

    public function store(StoreSpecialiteRequest $request) {
        $this->authorize('create', Specialite::class);
        $data = $request->validated();
        Specialite::create($data);
        return back()->with('success', 'Créé.');
    }

    public function update(Request $request, $id) {
        $spec = Specialite::findOrFail($id);
        $this->authorize('update', $spec);
        $data = $request->validate([
            'nom' => 'required|unique:specialites,nom,'.$id,
            'icone' => 'nullable|string|max:50',
            'tarif_consultation' => 'required|numeric|min:0',
        ]);
        $spec->update($data);
        return back()->with('success', 'Modifié.');
    }

    public function destroy($id) {
        $spec = Specialite::findOrFail($id);
        $this->authorize('delete', $spec);
        $spec->delete();
        return back()->with('error', 'Supprimé.');
    }
}
