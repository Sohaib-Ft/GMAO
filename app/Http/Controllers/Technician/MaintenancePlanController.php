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
        // Get current month and year
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);

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

        // Get all plans (no pagination for calendar view)
        $allPlans = $query->get();

        // Load active work orders for each plan
        foreach ($allPlans as $plan) {
            $plan->activeWorkOrder = \App\Models\WorkOrder::where('equipement_id', $plan->equipement_id)
                ->whereIn('statut', ['en_cours', 'en_attente'])
                ->latest()
                ->first();
        }

        // Group plans by date for calendar display
        $maintenanceByDate = [];
        $recurrenceService = new \App\Services\RecurrenceService();
        $startOfMonth = $currentDate->copy()->startOfMonth()->startOfWeek();
        $endOfMonth = $currentDate->copy()->endOfMonth()->endOfWeek();

        foreach ($allPlans as $plan) {
            if ($plan->rrule) {
                // Generer TOUTES les occurrences pour ce mois
                $occurrences = $recurrenceService->getOccurrencesBetween($plan->rrule, $plan->prochaine_date ?? now(), $endOfMonth);
                foreach ($occurrences as $occurrence) {
                    $dateKey = $occurrence->format('Y-m-d');
                    if (!isset($maintenanceByDate[$dateKey])) {
                        $maintenanceByDate[$dateKey] = [];
                    }
                    $maintenanceByDate[$dateKey][] = [
                        'plan' => $plan,
                        'date' => $occurrence,
                    ];
                }
            } elseif ($plan->prochaine_date) {
                // Fallback classique si pas de RRULE
                $dateKey = $plan->prochaine_date->format('Y-m-d');
                if (!isset($maintenanceByDate[$dateKey])) {
                    $maintenanceByDate[$dateKey] = [];
                }
                $maintenanceByDate[$dateKey][] = [
                    'plan' => $plan,
                    'date' => $plan->prochaine_date,
                ];
            }
        }

        // Also get list view for sidebar (Only show the IMMEDIATE NEXT occurrence per plan)
        $plansList = [];
        foreach ($allPlans as $plan) {
            if ($plan->rrule) {
                // Pour la liste, on ne prend que la TOUTE PROCHAINE occurrence (>= aujourd'hui)
                // On commence la recherche à partir de maintenant ou prochaine_date (la plus éloignée pour éviter le passé)
                $searchStart = $plan->prochaine_date && $plan->prochaine_date->isFuture() ? $plan->prochaine_date : now();
                $occurrences = $recurrenceService->getOccurrencesBetween($plan->rrule, $searchStart, now()->addMonth());
                
                if (!empty($occurrences)) {
                    $firstOcc = $occurrences[0];
                    $plansList[] = [
                        'plan' => $plan,
                        'date' => $firstOcc,
                        'prochaine_date' => $firstOcc,
                        'type' => $plan->type,
                        'activeWorkOrder' => $plan->activeWorkOrder,
                    ];
                }
            } elseif ($plan->prochaine_date) {
                $plansList[] = [
                    'plan' => $plan,
                    'date' => $plan->prochaine_date,
                    'prochaine_date' => $plan->prochaine_date,
                    'type' => $plan->type,
                    'activeWorkOrder' => $plan->activeWorkOrder,
                ];
            }
        }

        // Sort list by date
        usort($plansList, fn($a, $b) => $a['date']->timestamp <=> $b['date']->timestamp);
        $plans = collect($plansList)->take(30);

        return view('technician.maintenance_plans.index', compact('plans', 'maintenanceByDate', 'currentDate'));
    }

    /**
     * Start a maintenance plan by creating a work order.
     */
    public function start(MaintenancePlan $maintenancePlan)
    {
        // Try to find if requester passed a specific date (optional future enhancement)
        $targetDate = request('date') ? \Carbon\Carbon::parse(request('date')) : $maintenancePlan->prochaine_date;

        // Check if there is already an active work order
        if ($maintenancePlan->activeWorkOrder) {
            return back()->with('error', 'Une intervention est déjà en cours pour ce plan.');
        }

        // Check if it's too early to start (strict deadline)
        if ($targetDate && $targetDate->isFuture() && !$targetDate->isToday()) {
             return back()->with('error', 'Vous ne pouvez pas démarrer cette maintenance avant la date prévue (' . $targetDate->format('d/m/Y') . ').');
        }

        // Create the work order
        $workOrder = \App\Models\WorkOrder::create([
            'employe_id' => Auth::id(),
            'technicien_id' => Auth::id(),
            'equipement_id' => $maintenancePlan->equipement_id,
            'maintenance_plan_id' => $maintenancePlan->id,
            'titre' => 'Maintenance ' . ucfirst($maintenancePlan->type) . ' - ' . $maintenancePlan->equipement->nom,
            'description' => "Intervention générée automatiquement depuis le plan de maintenance.\nType: " . ucfirst($maintenancePlan->type) . ($targetDate ? "\nDate prévue: " . $targetDate->format('d/m/Y') : ""),
            'priorite' => 'normale',
            'statut' => 'en_cours',
            'date_creation' => now(),
            'date_debut' => now(),
        ]);

        return back()->with('status', 'Intervention démarrée avec succès. Bon de travail #' . $workOrder->id . ' créé.');
    }
}
