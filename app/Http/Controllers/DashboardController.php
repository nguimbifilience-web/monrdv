<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use App\Models\Medecin;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function index()
    {
        $nbRendezvous = RendezVous::whereDate('date_rv', today())->count();
        $nbMedecins = Medecin::count();
        $nbPatients = Patient::count();

        return view('dashboard', compact('nbRendezvous', 'nbMedecins', 'nbPatients'));
    }
}
