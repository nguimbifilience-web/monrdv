<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit-modern', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Mise à jour infos
        if ($request->filled('name') || $request->filled('email')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            $user->name = $request->name;
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->email = $request->email;
            $user->save();

            return back()->with('success', 'Informations mises à jour.');
        }

        // Changement mot de passe
        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => 'required|current_password',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Mot de passe modifié.');
        }

        return back();
    }
}
