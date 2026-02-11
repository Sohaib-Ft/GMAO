<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipments for employees.
     */
    public function index(Request $request)
    {
        $query = Equipement::query();

        // Search logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('marque', 'LIKE', "%{$search}%")
                  ->orWhere('modele', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('localisation')) {
            $query->where('localisation', 'LIKE', "%{$request->localisation}%");
        }

        $equipments = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return view('employee.equipments._list_partial', compact('equipments'));
        }

        return view('employee.equipments.index', compact('equipments'));
    }

    /**
     * Display the specified equipment.
     */
    public function show(Equipement $equipment)
    {
        return view('employee.equipments.show', compact('equipment'));
    }
}
