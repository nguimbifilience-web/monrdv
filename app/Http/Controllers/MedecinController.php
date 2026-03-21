<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin;
use App\Models\Specialite;
use Illuminate\Support\Facades\Auth;

class MedecinController extends Controller
{
    public function __construct()
    {
        // Seuls les utilisateurs connectés ET administrateurs peuvent entrer
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->is_admin) {
                return redirect('/')->with('error', 'Accès réservé à l\'administrateur.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $medecins = Medecin::with('specialite')->orderBy('nom', 'asc')->get();
        return view('medecins.index', compact('medecins'));
    }

    public function create()
    {
        $specialites = Specialite::all();
        return view('medecins.create', compact('specialites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'specialite_id' => 'required|exists:specialites,id',
        ]);

        Medecin::create($request->all());

        return redirect()->route('medecins.index')->with('success', 'Médecin ajouté avec succès.');
    }
}