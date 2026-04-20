<?php

namespace App\Http\Controllers;

use App\Models\Assurance;
use App\Models\Patient;
use App\Http\Requests\StoreAssuranceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssuranceController extends Controller
{
    public function index(Request $request) {
        $this->authorize('viewAny', Assurance::class);
        $search = $request->get('search');
        $query = Assurance::withCount('patients');

        if ($search) {
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('nom_referent', 'like', "%{$search}%");
        }

        $assurances = $query->orderBy('created_at', 'desc')->get();
        $total = $assurances->count();

        // Graphique optimisé : une seule requête au lieu de N*6
        $moisLabels = [];
        $dates = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $moisLabels[] = $date->translatedFormat('M Y');
            $dates[] = ['month' => $date->month, 'year' => $date->year];
        }

        // Une seule requête groupée
        $stats = Patient::select('assurance_id',
                DB::raw('MONTH(created_at) as mois'),
                DB::raw('YEAR(created_at) as annee'),
                DB::raw('COUNT(*) as total'))
            ->whereNotNull('assurance_id')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('assurance_id', DB::raw('MONTH(created_at)'), DB::raw('YEAR(created_at)'))
            ->get()
            ->groupBy('assurance_id');

        $colors = ['#06b6d4','#3b82f6','#8b5cf6','#f59e0b','#ef4444','#10b981','#ec4899','#6366f1'];
        $datasets = [];

        foreach ($assurances as $i => $a) {
            $data = [];
            foreach ($dates as $d) {
                $count = $stats->get($a->id)?->where('mois', $d['month'])->where('annee', $d['year'])->first();
                $data[] = $count ? $count->total : 0;
            }
            $color = $colors[$i % count($colors)];
            $datasets[] = [
                'label' => $a->nom,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color . '20',
                'tension' => 0.4,
                'fill' => true,
            ];
        }

        $chartData = ['labels' => $moisLabels, 'datasets' => $datasets];

        return view('assurances.index', compact('assurances', 'total', 'chartData'));
    }

    public function store(StoreAssuranceRequest $request) {
        $this->authorize('create', Assurance::class);
        $data = $request->validated();

        if ($request->hasFile('document_modele')) {
            $data['document_modele_path'] = $request->file('document_modele')->store('assurances/documents', 'public');
        }

        Assurance::create($data);
        return back()->with('success', 'Partenaire ajouté.');
    }

    public function update(StoreAssuranceRequest $request, $id) {
        $assurance = Assurance::findOrFail($id);
        $this->authorize('update', $assurance);
        $data = $request->validated();

        if ($request->hasFile('document_modele')) {
            if ($assurance->document_modele_path) {
                Storage::disk('public')->delete($assurance->document_modele_path);
            }
            $data['document_modele_path'] = $request->file('document_modele')->store('assurances/documents', 'public');
        }

        $assurance->update($data);
        return back()->with('success', 'Contacts mis à jour.');
    }

    public function destroyDocument($id) {
        $assurance = Assurance::findOrFail($id);
        $this->authorize('update', $assurance);
        if ($assurance->document_modele_path) {
            Storage::disk('public')->delete($assurance->document_modele_path);
            $assurance->update(['document_modele_path' => null]);
        }
        return back()->with('success', 'Document supprimé.');
    }

    public function destroy($id) {
        $assurance = Assurance::findOrFail($id);
        $this->authorize('delete', $assurance);
        if ($assurance->document_modele_path) {
            Storage::disk('public')->delete($assurance->document_modele_path);
        }
        $assurance->delete();
        return back()->with('error', 'Partenaire supprimé.');
    }
}
