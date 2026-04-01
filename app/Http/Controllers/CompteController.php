<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompteController extends Controller
{
    public function index()
    {
        $comptes = User::where('role', '!=', 'super_admin')
            ->orderByRaw("FIELD(role, 'admin', 'secretaire', 'medecin', 'patient')")
            ->orderBy('name')
            ->get();

        return view('comptes.index', compact('comptes'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:4|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['plain_password'] = $request->password;
        }

        $user->update($data);

        return back()->with('success', "Compte de {$user->name} mis à jour.");
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $newPassword = Str::random(8);

        $user->update([
            'password' => Hash::make($newPassword),
            'plain_password' => $newPassword,
        ]);

        return back()->with('success', "Mot de passe de {$user->name} réinitialisé : {$newPassword}");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return back()->with('success', "Compte de {$user->name} supprimé.");
    }
}
