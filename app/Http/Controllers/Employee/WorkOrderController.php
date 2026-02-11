<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WorkOrder;
use App\Models\Equipement;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    /**
     * Show form to create a new work order (breakdown report).
     */
    public function create()
    {
        $equipments = Equipement::with('equipmentType')
            ->where('statut', '!=', 'inactif')
            ->orderBy('nom')
            ->get();
            
        return view('employee.workorders.create', compact('equipments'));
    }

    /**
     * Display a listing of the employee's work orders.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = WorkOrder::where('employe_id', $userId)
                        ->with('equipement');

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

        $workOrders = $query->latest('date_creation')->paginate(10);

        // Stats for cards
        $stats = [
            'total' => WorkOrder::where('employe_id', $userId)->count(),
            'attente' => WorkOrder::where('employe_id', $userId)->where('statut', 'en_attente')->count(),
            'cours' => WorkOrder::where('employe_id', $userId)->where('statut', 'en_cours')->count(),
            'termine' => WorkOrder::where('employe_id', $userId)->where('statut', 'terminee')->count(),
        ];

        return view('employee.workorders.index', compact('workOrders', 'stats'));
    }

    /**
     * Store the new work order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipement_id' => 'required|exists:equipements,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'priorite' => 'required|in:basse,normale,haute,urgente'
        ]);

        $workOrder = WorkOrder::create([
            'employe_id' => Auth::id(),
            'equipement_id' => $request->equipement_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'priorite' => $request->priorite,
            'statut' => 'en_attente',
            'date_creation' => now(),
        ]);

        // L'équipement reste actif même en cas de panne signalée (demande utilisateur)
        // if ($workOrder->equipement) {
        //     $workOrder->equipement->update(['statut' => 'panne']);
        // }

        return redirect()->route('employee.workorders.index')->with('status', 'Votre demande d\'intervention a été enregistrée avec succès.');
    }

    /**
     * Remove the specified work order from storage.
     */
    public function destroy(WorkOrder $workOrder)
    {
        // Vérifier que l'ordre appartient bien à l'utilisateur connecté
        if ($workOrder->employe_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à supprimer cet ordre de travail.');
        }

        $workOrder->delete();

        return redirect()->route('employee.workorders.index')->with('status', 'Ordre de travail supprimé avec succès.');
    }
}
