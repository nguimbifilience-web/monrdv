<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Patient;
use App\Models\Medecin;
use Illuminate\Http\Request;

class RendezVousController extends Controller
{
    public function index(Request $request)
    {
        $query = RendezVous::with(['patient', 'medecin']);

        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date_rv', $request->date);
        }

        if ($request->filled('medecin_id')) {
            $query->where('medecin_id', $request->medecin_id);
        }

        $rendezvous = $query->orderBy('date_rv', 'desc')->orderBy('heure_rv', 'asc')->get();
        $medecins = Medecin::all();

        return view('rendezvous.index', compact('rendezvous', 'medecins'));
    }

    public function create()
    {
        $patients = Patient::all();
        $medecins = Medecin::all();
        return view('rendezvous.create', compact('patients', 'medecins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_rv'    => 'required|date',
            'heure_rv'   => 'required',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif'      => 'nullable|string|max:255',
        ]);

        RendezVous::create($validated);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Le rendez-vous a été enregistré !');
    }

    public function edit($id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $patients = Patient::all();
        $medecins = Medecin::all();

        return view('rendezvous.edit', compact('rendezvous', 'patients', 'medecins'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date_rv'    => 'required|date',
            'heure_rv'   => 'required',
            'patient_id' => 'required|exists:patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'motif'      => 'nullable|string|max:255',
        ]);

        $rendezvous = RendezVous::findOrFail($id);
        $rendezvous->update($validated);

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous mis à jour avec succès !');
    }

    public function destroy($id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        $rendezvous->delete();

        return redirect()->route('rendezvous.index')
                         ->with('success', 'Rendez-vous supprimé.');
    }
}
