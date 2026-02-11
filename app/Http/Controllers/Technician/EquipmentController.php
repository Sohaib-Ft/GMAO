<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipments (read-only for technician).
     */
    public function index(Request $request)
    {
        // Use same query/filters as Employee controller so views match
        $query = Equipement::with(['localisation', 'equipmentType']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('localisation')) {
            $query->where('localisation', 'LIKE', "%{$request->localisation}%");
        }

        $equipments = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return view('technician.equipments._list_partial', compact('equipments'));
        }

        return view('technician.equipments.index', compact('equipments'));
    }

    /**
     * Display the specified equipment (read-only).
     */
    public function show(Equipement $equipment)
    {
        $equipment->load(['localisation', 'equipmentType', 'workOrders']);
        return view('technician.equipments.show', compact('equipment'));
    }
}
