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
            report($e);
            return response()->json(['status' => 'error', 'message' => 'Une erreur est survenue lors de la modification.'], 500);
        }
    }
}