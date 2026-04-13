<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalAssurance;
use App\Models\GlobalSpecialite;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // ---------- Spécialités globales ----------

    public function specialites()
    {
        $specialites = GlobalSpecialite::orderBy('nom')->get();
        return view('superadmin.settings.specialites', compact('specialites'));
    }

    public function storeSpecialite(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'icone' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        GlobalSpecialite::create($data + ['is_active' => true]);
        return back()->with('success', 'Spécialité globale ajoutée.');
    }

    public function updateSpecialite(Request $request, GlobalSpecialite $specialite)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'icone' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $specialite->update($data);
        return back()->with('success', 'Spécialité mise à jour.');
    }

    public function destroySpecialite(GlobalSpecialite $specialite)
    {
        $specialite->delete();
        return back()->with('success', 'Spécialité supprimée.');
    }

    // ---------- Assurances globales ----------

    public function assurances()
    {
        $assurances = GlobalAssurance::orderBy('nom')->get();
        return view('superadmin.settings.assurances', compact('assurances'));
    }

    public function storeAssurance(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'icone' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:150',
        ]);
        GlobalAssurance::create($data + ['is_active' => true]);
        return back()->with('success', 'Assurance globale ajoutée.');
    }

    public function updateAssurance(Request $request, GlobalAssurance $assurance)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:150',
            'icone' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:150',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $assurance->update($data);
        return back()->with('success', 'Assurance mise à jour.');
    }

    public function destroyAssurance(GlobalAssurance $assurance)
    {
        $assurance->delete();
        return back()->with('success', 'Assurance supprimée.');
    }

    // ---------- Configuration système ----------

    public function system()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('superadmin.settings.system', compact('settings'));
    }

    public function updateSystem(Request $request)
    {
        $input = $request->input('settings', []);
        foreach ($input as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'Paramètres système mis à jour.');
    }

    // ---------- Apparence (thème couleurs) ----------

    public function appearance()
    {
        $theme = Setting::theme();
        return view('superadmin.settings.appearance', compact('theme'));
    }

    public function updateAppearance(Request $request)
    {
        $data = $request->validate([
            'theme_bg_page' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'theme_bg_card' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'theme_text_primary' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'theme_accent' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'theme_sidebar_bg' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        foreach ($data as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            } else {
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'type' => 'string',
                    'group' => 'appearance',
                ]);
            }
        }

        Setting::clearThemeCache();

        return back()->with('success', 'Apparence mise à jour.');
    }
}
