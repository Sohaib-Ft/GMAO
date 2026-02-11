<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the work orders.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['employe', 'technicien', 'equipement']);

        // Search
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

        $workOrders = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('admin.workorders._list_partial', compact('workOrders'));
        }

        return view('admin.workorders.index', compact('workOrders'));
    }

    /**
     * Display the specified work order.
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['employe', 'technicien', 'equipement', 'interventions.technicien']);
        return view('admin.workorders.show', compact('workOrder'));
    }

    /**
     * Show the form for editing the specified work order.
     */
    public function edit(WorkOrder $workOrder)
    {
        return view('admin.workorders.edit', compact('workOrder'));
    }
}
