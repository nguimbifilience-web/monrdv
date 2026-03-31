<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::withCount(['users', 'patients', 'medecins'])->latest()->get();
        return view('clinics.index', compact('clinics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $clinic = Clinic::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clinics.index')->with('success', "Clinique « {$clinic->name} » créée.");
    }

    public function update(Request $request, Clinic $clinic)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $clinic->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('clinics.index')->with('success', 'Clinique mise à jour.');
    }

    public function toggleActive(Clinic $clinic)
    {
        $clinic->update(['is_active' => !$clinic->is_active]);
        $status = $clinic->is_active ? 'activée' : 'désactivée';
        return redirect()->route('clinics.index')->with('success', "Clinique {$status}.");
    }

    public function destroy(Clinic $clinic)
    {
        $clinic->delete();
        return redirect()->route('clinics.index')->with('success', 'Clinique supprimée.');
    }
}
