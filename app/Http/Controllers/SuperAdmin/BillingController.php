<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = Clinic::with('plan');

        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($planSlug = $request->get('plan')) {
            $query->whereHas('plan', fn ($q) => $q->where('slug', $planSlug));
        }
        if ($status = $request->get('status')) {
            $today = now()->startOfDay();
            match ($status) {
                'active' => $query->whereDate('subscription_expires_at', '>=', $today->copy()->addDays(16)),
                'expiring' => $query->whereBetween('subscription_expires_at', [$today, $today->copy()->addDays(15)]),
                'expired' => $query->whereDate('subscription_expires_at', '<', $today),
                default => null,
            };
        }

        $clinics = $query->orderBy('name')->paginate(20)->withQueryString();
        $plans = Plan::where('is_active', true)->orderBy('price_monthly')->get();

        return view('superadmin.billing.index', compact('clinics', 'plans'));
    }

    public function updateSubscription(Request $request, Clinic $clinic)
    {
        $request->validate([
            'plan_id' => 'nullable|exists:plans,id',
            'subscription_started_at' => 'nullable|date',
            'subscription_expires_at' => 'nullable|date|after_or_equal:subscription_started_at',
        ]);

        $clinic->update($request->only('plan_id', 'subscription_started_at', 'subscription_expires_at'));
        return back()->with('success', "Abonnement de « {$clinic->name} » mis à jour.");
    }

    // ---------- Plans catalog ----------

    public function plans()
    {
        $plans = Plan::withCount('clinics')->orderBy('price_monthly')->get();
        return view('superadmin.billing.plans', compact('plans'));
    }

    public function storePlan(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:plans,slug',
            'price_monthly' => 'required|integer|min:0',
            'max_medecins' => 'nullable|integer|min:1',
            'max_rdv_monthly' => 'nullable|integer|min:1',
            'includes_insurance' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);
        $data['includes_insurance'] = (bool) ($data['includes_insurance'] ?? false);
        Plan::create($data + ['is_active' => true]);
        return back()->with('success', 'Plan créé.');
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'price_monthly' => 'required|integer|min:0',
            'max_medecins' => 'nullable|integer|min:1',
            'max_rdv_monthly' => 'nullable|integer|min:1',
            'includes_insurance' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);
        $data['includes_insurance'] = (bool) ($data['includes_insurance'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $plan->update($data);
        return back()->with('success', "Plan « {$plan->name} » mis à jour.");
    }

    public function destroyPlan(Plan $plan)
    {
        if ($plan->clinics()->exists()) {
            return back()->withErrors(['plan' => "Impossible de supprimer : des cliniques utilisent ce plan."]);
        }
        $plan->delete();
        return back()->with('success', 'Plan supprimé.');
    }
}
