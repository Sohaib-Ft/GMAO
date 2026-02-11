<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InterventionController extends Controller
{
    /**
     * Start an intervention (changes status to en_cours).
     */
    public function start(WorkOrder $workOrder)
    {
        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403);
        }

        // Create the intervention record
        Intervention::create([
            'work_order_id' => $workOrder->id,
            'technicien_id' => Auth::id(),
            'date_debut' => Carbon::now(),
            'description' => 'Début de l\'intervention'
        ]);

        // Update WorkOrder status
        $workOrder->update(['statut' => 'en_cours']);

        // Update Equipment status to 'en maintenance'
        if ($workOrder->equipement) {
            $workOrder->equipement->update(['statut' => 'en maintenance']);
        }

        return back()->with('status', 'Intervention démarrée. L\'équipement est maintenant en maintenance.');
    }

    /**
     * Terminate an intervention and close the WorkOrder.
     */
    public function complete(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'cout' => 'nullable|numeric'
        ]);

        if ($workOrder->technicien_id !== Auth::id()) {
            abort(403);
        }

        // Find the active intervention (the one without date_fin)
        $intervention = Intervention::where('work_order_id', $workOrder->id)
            ->where('technicien_id', Auth::id())
            ->whereNull('date_fin')
            ->latest()
            ->first();

        if ($intervention) {
            $intervention->update([
                'date_fin' => Carbon::now(),
                'description' => $request->description,
                'cout' => $request->cout ?? 0
            ]);
        }

        // Close the WorkOrder
        $workOrder->update(['statut' => 'terminee']);

        // Update Equipment status back to 'actif'
        if ($workOrder->equipement) {
            $workOrder->equipement->update(['statut' => 'actif']);
        }

        return redirect()->route('technician.workorders.index')->with('status', 'Travail terminé. L\'équipement est de nouveau actif.');
    }
}
