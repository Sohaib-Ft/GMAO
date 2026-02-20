<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Equipement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    /**
     * Show form to create a new work order.
     */
    public function create(Request $request)
    {
        $equipments = Equipement::where('statut', 'actif')
            ->orderBy('nom')
            ->get();
            
        $selectedEquipmentId = $request->get('equipement_id');
        $maintenancePlanId = $request->get('maintenance_plan_id');
        $type = $request->get('type', 'corrective');

        return view('technician.workorders.create', compact('equipments', 'selectedEquipmentId', 'maintenancePlanId', 'type'));
    }

    /**
     * Store a newly created work order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipement_id' => 'required|exists:equipements,id',
            'maintenance_plan_id' => 'nullable|exists:maintenance_plans,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'priorite' => 'required|in:basse,normale,haute,urgente'
        ]);

        $workOrder = WorkOrder::create([
            'employe_id' => Auth::id(),
            'technicien_id' => Auth::id(),
            'equipement_id' => $request->equipement_id,
            'maintenance_plan_id' => $request->maintenance_plan_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'statut' => 'en_cours',
            'date_creation' => now(),
            'date_debut' => now(),
        ]);

        return redirect()->route('technician.workorders.index')->with('status', 'L\'ordre de travail a été créé et démarré avec succès.');
    }
    /**
     * Display a listing of assigned work orders.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['equipement', 'employe'])
            ->whereNotIn('statut', ['terminee', 'annulee']);

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('equipement', function($eq) use ($search) {
                      $eq->where('nom', 'LIKE', "%{$search}%")
                         ->orWhere('code', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filter by priority
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        // Filter by assignment status
        if ($request->filled('assignation')) {
            if ($request->assignation === 'moi') {
                $query->where('technicien_id', Auth::id());
            } elseif ($request->assignation === 'assigne') {
                $query->whereNotNull('technicien_id')
                      ->where('technicien_id', '!=', Auth::id());
            } elseif ($request->assignation === 'non_assigne') {
                $query->whereNull('technicien_id');
            }
        }

        $workOrders = $query->latest()->paginate(10);

        return view('technician.workorders.index', compact('workOrders'));
    }

    /**
     * Display the specified work order details.
     */
    public function show(WorkOrder $workOrder)
    {
        // Ensure the work order is assigned to this technician
        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403, 'Cet ordre de travail ne vous est pas assigné.');
        }

        $workOrder->load(['equipement', 'employe', 'interventions']);
        
        return view('technician.workorders.show', compact('workOrder'));
    }

    /**
     * Display a listing of completed work orders.
     */
    public function history()
    {
        // Exclude work orders created from maintenance plans (préventive)
        $workOrders = WorkOrder::with(['equipement'])
            ->where('technicien_id', Auth::id())
            ->where('statut', 'terminee')
            ->whereNull('maintenance_plan_id')
            ->latest()
            ->paginate(10);

        return view('technician.workorders.history', compact('workOrders'));
    }

    /**
     * Display all active work orders (assigned and unassigned).
     */
    public function available(Request $request)
    {
        $query = WorkOrder::with(['equipement', 'employe', 'technicien'])
            ->whereNotIn('statut', ['terminee', 'annulee']);

        // Search logic (Title or Description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titre', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('equipement', function($eq) use ($search) {
                      $eq->where('code', 'LIKE', "%{$search}%")
                         ->orWhere('nom', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filter by priority
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        // Filter by location
        if ($request->filled('localisation')) {
            $query->whereHas('equipement', function($q) use ($request) {
                $q->where('localisation', 'LIKE', "%{$request->localisation}%");
            });
        }

        $workOrders = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('technician.workorders._available_list_partial', compact('workOrders'));
        }

        return view('technician.workorders.available', compact('workOrders'));
    }

    /**
     * Self-assign a work order.
     */
    public function assign(WorkOrder $workOrder)
    {
        if ($workOrder->technicien_id !== null) {
            return back()->with('error', 'Cet ordre de travail est déjà assigné.');
        }

        $workOrder->update([
            'technicien_id' => Auth::id(),
            'statut' => 'en_attente'
        ]);

        // L'équipement n'est pas encore en maintenance tant que le travail n'a pas commencé

        return redirect()->route('technician.workorders.index')
            ->with('status', 'L\'ordre de travail vous a été assigné avec succès. Cliquez sur "Démarrer" pour commencer.');
    }

    /**
     * Start working on a work order.
     */
    public function start(WorkOrder $workOrder)
    {
        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403, 'Cet ordre de travail ne vous est pas assigné.');
        }

        if ($workOrder->statut === 'en_cours') {
            return back()->with('error', 'L\'ordre de travail est déjà en cours.');
        }

        $workOrder->update([
            'statut' => 'en_cours'
        ]);

        // L'équipement reste actif (demande utilisateur)
        // if ($workOrder->equipement) {
        //     $workOrder->equipement->update(['statut' => 'maintenance']);
        // }

        return back()->with('status', 'Ordre de travail démarré avec succès.');
    }

    /**
     * Complete a work order successfully.
     */
    public function complete(WorkOrder $workOrder)
    {
        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403, 'Cet ordre de travail ne vous est pas assigné.');
        }

        // Enforce deadline check if linked to a maintenance plan
        if ($workOrder->maintenance_plan_id) {
            $plan = $workOrder->maintenancePlan;
            if ($plan && $plan->prochaine_date && $plan->prochaine_date->isFuture()) {
                return back()->with('error', 'Vous ne pouvez pas terminer cette intervention avant la date d\'échéance prévue (' . $plan->prochaine_date->format('d/m/Y') . ').');
            }
        }

        $workOrder->update([
            'statut' => 'terminee'
        ]);

        // If linked to a maintenance plan, update the plan dates
        if ($workOrder->maintenance_plan_id) {
            $plan = $workOrder->maintenancePlan;
            if ($plan) {
                $plan->update([
                    'derniere_date' => now(),
                    'prochaine_date' => now()->addDays((int)$plan->interval_jours)
                ]);
            }
        }

        // Plus besoin de remettre en actif car il ne change plus (demande utilisateur)
        // if ($workOrder->equipement) {
        //     $workOrder->equipement->update(['statut' => 'actif']);
        // }

        // Notifier l'employé
        if ($workOrder->employe) {
            $workOrder->employe->notifications()->create([
                'type' => 'maintenance_terminee',
                'message' => "La maintenance de votre équipement \"{$workOrder->equipement->nom}\" est terminée.",
                'data' => json_encode([
                    'work_order_id' => $workOrder->id,
                    'equipement_id' => $workOrder->equipement_id,
                    'equipement_nom' => $workOrder->equipement->nom ?? '',
                ])
            ]);
        }

        return redirect()->route('technician.workorders.index')
            ->with('status', 'Ordre de travail terminé avec succès. L\'équipement est de nouveau opérationnel.');
    }

    /**
     * Mark equipment as irreparable/destroyed.
     */
    public function markAsIrrepairable(WorkOrder $workOrder, Request $request)
    {
        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403, 'Cet ordre de travail ne vous est pas assigné.');
        }

        $request->validate([
            'raison' => 'nullable|string|max:500'
        ]);

        $workOrder->update([
            'statut' => 'terminee',
            'description' => $workOrder->description . "\n\n[ÉQUIPEMENT IRRÉPARABLE] " . $request->raison
        ]);

        // Marquer l'équipement comme inactif
        if ($workOrder->equipement) {
            $workOrder->equipement->update(['statut' => 'inactif']);
        }

        // Notifier l'employé
        if ($workOrder->employe) {
            $workOrder->employe->notifications()->create([
                'type' => 'equipement_irreparable',
                'message' => "Votre équipement \"{$workOrder->equipement->nom}\" ne peut pas être réparé. Raison : {$request->raison}",
                'data' => json_encode([
                    'work_order_id' => $workOrder->id,
                    'equipement_id' => $workOrder->equipement_id,
                    'equipement_nom' => $workOrder->equipement->nom ?? '',
                    'raison' => $request->raison
                ])
            ]);

            // Envoyer l'email
            if ($workOrder->employe->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($workOrder->employe->email)
                        ->send(new \App\Mail\EquipmentIrreparableMail($workOrder, $request->raison));
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    \Illuminate\Support\Facades\Log::error('Failed to send irreparable equipment email: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('technician.workorders.index')
            ->with('status', 'L\'équipement a été marqué comme irréparable et l\'employé a été notifié.');
    }
}
