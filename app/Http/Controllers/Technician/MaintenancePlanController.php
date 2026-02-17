<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenancePlanController extends Controller
{
    /**
     * Display a listing of active maintenance plans for technicians.
     */
    public function index(Request $request)
    {
        $query = MaintenancePlan::where(function($q) {
                $q->where('technicien_id', Auth::id())
                  ->orWhereNull('technicien_id');
            })
            ->with(['equipement.equipmentType', 'equipement.localisationRelation'])
            ->where('statut', 'actif');

        // Search by equipment name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('equipement', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        // Filter by type (preventive/corrective)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort by next maintenance date (soonest first)
        $plans = $query->orderBy('prochaine_date', 'asc')->paginate(15);

        return view('technician.maintenance_plans.index', compact('plans'));
    }

    /**
     * Start a maintenance plan by creating a work order.
     */
    public function start(MaintenancePlan $maintenancePlan)
    {
        // Check if there is already an active work order
        if ($maintenancePlan->activeWorkOrder) {
            return back()->with('error', 'Une intervention est déjà en cours pour ce plan.');
        }

        // Check if it's too early to start (strict deadline)
        if ($maintenancePlan->prochaine_date && $maintenancePlan->prochaine_date->isFuture() && !$maintenancePlan->prochaine_date->isToday()) {
             return back()->with('error', 'Vous ne pouvez pas démarrer cette maintenance avant la date prévue (' . $maintenancePlan->prochaine_date->format('d/m/Y') . ').');
        }

        // Create the work order
        $workOrder = \App\Models\WorkOrder::create([
            'technicien_id' => Auth::id(),
            'equipement_id' => $maintenancePlan->equipement_id,
            'maintenance_plan_id' => $maintenancePlan->id,
            'titre' => 'Maintenance ' . ucfirst($maintenancePlan->type) . ' - ' . $maintenancePlan->equipement->nom,
            'description' => "Intervention générée automatiquement depuis le plan de maintenance.\nType: " . ucfirst($maintenancePlan->type) . "\nIntervalle: " . $maintenancePlan->interval_jours . " jours",
            'priorite' => 'normale',
            'statut' => 'en_cours',
            'date_creation' => now(),
            'date_debut' => now(),
        ]);

        return back()->with('status', 'Intervention démarrée avec succès. Bon de travail #' . $workOrder->id . ' créé.');
    }
}
