<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePlan;
use App\Models\Equipement;
use App\Models\User;
use App\Http\Requests\StoreMaintenancePlanRequest;
use App\Http\Requests\UpdateMaintenancePlanRequest;
use Illuminate\Http\Request;

class MaintenancePlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MaintenancePlan::with(['equipement.equipmentType', 'equipement.localisationRelation']);

        // Filter by equipment
        if ($request->filled('equipement_id')) {
            $query->where('equipement_id', $request->equipement_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Search by equipment name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('equipement', function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $plans = $query->latest()->paginate(15);
        $equipments = Equipement::orderBy('nom')->get();

        return view('admin.maintenance_plans.index', compact('plans', 'equipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipments = Equipement::where('statut', 'actif')
            ->with(['equipmentType', 'localisationRelation'])
            ->orderBy('nom')
            ->get();
        
        $technicians = User::where('role', 'technicien')->where('is_active', true)->orderBy('name')->get();

        return view('admin.maintenance_plans.create', compact('equipments', 'technicians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipement_id' => 'required|exists:equipements,id',
            'type' => 'required|in:preventive',
            'rrule' => 'nullable|string',
            'interval_jours' => 'nullable|integer|min:1',
            'technicien_id' => 'required|exists:users,id',
            'statut' => 'required|in:actif,inactif',
        ]);

        $plan = MaintenancePlan::create($validated);
        
        // Initial next date calculation
        $plan->updateNextDate(now());

        return redirect()->route('maintenance-plans.index')
            ->with('status', 'Plan de maintenance créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->load(['equipement.equipmentType', 'equipement.localisationRelation']);
        
        return view('admin.maintenance_plans.show', compact('maintenancePlan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenancePlan $maintenancePlan)
    {
        $equipments = Equipement::where('statut', 'actif')
            ->with(['equipmentType', 'localisationRelation'])
            ->orderBy('nom')
            ->get();
        
        $technicians = User::where('role', 'technicien')->where('is_active', true)->orderBy('name')->get();

        return view('admin.maintenance_plans.edit', compact('maintenancePlan', 'equipments', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenancePlan $maintenancePlan)
    {
        $validated = $request->validate([
            'equipement_id' => 'required|exists:equipements,id',
            'type' => 'required|in:preventive',
            'rrule' => 'nullable|string',
            'interval_jours' => 'nullable|integer|min:1',
            'technicien_id' => 'required|exists:users,id',
            'statut' => 'required|in:actif,inactif',
        ]);

        $maintenancePlan->update($validated);
        
        // Refresh next date if rrule was changed
        if ($maintenancePlan->wasChanged('rrule') || $maintenancePlan->wasChanged('interval_jours')) {
             $maintenancePlan->updateNextDate(now());
        }

        return redirect()->route('maintenance-plans.index')
            ->with('status', 'Plan de maintenance mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->delete();

        return redirect()->route('maintenance-plans.index')
            ->with('status', 'Plan de maintenance supprimé avec succès.');
    }

    /**
     * API endpoint to search equipments for autocomplete
     */
    public function searchEquipments(Request $request)
    {
        $search = $request->get('q', '');
        
        $equipments = Equipement::where('statut', 'actif')
            ->where(function($query) use ($search) {
                $query->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
            })
            ->with(['equipmentType', 'localisationRelation'])
            ->limit(20)
            ->get()
            ->map(function($equipment) {
                return [
                    'id' => $equipment->id,
                    'code' => $equipment->code,
                    'nom' => $equipment->nom,
                    'type' => $equipment->equipmentType->name ?? '',
                    'localisation' => $equipment->localisationRelation->name ?? '',
                ];
            });

        return response()->json($equipments);
    }

    /**
     * API endpoint to get a single equipment by ID
     */
    public function getEquipment($id)
    {
        $equipment = Equipement::with(['equipmentType', 'localisationRelation'])
            ->findOrFail($id);

        return response()->json([
            'id' => $equipment->id,
            'code' => $equipment->code,
            'nom' => $equipment->nom,
            'type' => $equipment->equipmentType->name ?? '',
            'localisation' => $equipment->localisationRelation->name ?? '',
        ]);
    }
}
