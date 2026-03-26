<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disponibilite;

class DisponibiliteController extends Controller
{
    public function toggle(Request $request)
    {
        try {
            $dispo = Disponibilite::where('medecin_id', $request->medecin_id)
                                 ->where('date_travail', $request->date)
                                 ->first();

            if ($dispo) {
                $dispo->delete();
                return response()->json(['status' => 'removed']);
            }

            Disponibilite::create([
                'medecin_id' => $request->medecin_id,
                'date_travail' => $request->date
            ]);

            return response()->json(['status' => 'added']);

        } catch (\Exception $e) {
            // Si ça plante, on renvoie l'erreur précise au lieu d'un message générique
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}