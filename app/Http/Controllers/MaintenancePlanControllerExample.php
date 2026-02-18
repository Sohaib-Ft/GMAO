<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenancePlanRequest;
use App\Models\MaintenancePlan;
use App\Services\RruleParser;
use Illuminate\Support\Facades\Log;

class MaintenancePlanController extends Controller
{
    /**
     * Affiche le formulaire de création d'un plan de maintenance
     */
    public function create()
    {
        $technicians = \App\Models\User::where('role', '!=', 'admin')->get();
        return view('admin.maintenance_plans.create', [
            'technicians' => $technicians,
        ]);
    }

    /**
     * Stocke un nouveau plan de maintenance
     */
    public function store(StoreMaintenancePlanRequest $request)
    {
        try {
            $validated = $request->validated();

            // Parser la RRULE pour l'affichage
            $parser = new RruleParser($validated['rrule']);
            
            Log::info('Creating maintenance plan', [
                'rrule' => $validated['rrule'],
                'frequency' => $parser->getFrequency(),
                'interval' => $parser->getInterval(),
                'french_summary' => $parser->toFrench(),
            ]);

            // Créer le plan
            $plan = MaintenancePlan::create($validated);

            // Optionnel: Générer les prochaines occurrences
            // Cette logique peut être déléguée à un job ou à un service
            $this->generateNextOccurrences($plan);

            return redirect()
                ->route('maintenance-plans.show', $plan)
                ->with('success', 'Plan de maintenance créé avec succès');

        } catch (\Exception $e) {
            Log::error('Error creating maintenance plan', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return back()
                ->withInput()
                ->withErrors('Une erreur est survenue lors de la création du plan');
        }
    }

    /**
     * Affiche un plan de maintenance
     */
    public function show(MaintenancePlan $maintenancePlan)
    {
        $parser = new RruleParser($maintenancePlan->rrule);
        
        return view('admin.maintenance_plans.show', [
            'plan' => $maintenancePlan,
            'rrule_summary' => $parser->toFrench(),
            'rrule_details' => $parser->getParts(),
            'next_occurrences' => $parser->getNextOccurrences(now(), 10),
        ]);
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(MaintenancePlan $maintenancePlan)
    {
        $technicians = \App\Models\User::where('role', '!=', 'admin')->get();
        
        return view('admin.maintenance_plans.edit', [
            'plan' => $maintenancePlan,
            'technicians' => $technicians,
        ]);
    }

    /**
     * Met à jour un plan de maintenance
     */
    public function update(StoreMaintenancePlanRequest $request, MaintenancePlan $maintenancePlan)
    {
        try {
            $validated = $request->validated();
            
            $maintenancePlan->update($validated);

            // Régénérer les occurrences si la RRULE a changé
            if ($maintenancePlan->wasChanged('rrule')) {
                $this->generateNextOccurrences($maintenancePlan);
            }

            return redirect()
                ->route('maintenance-plans.show', $maintenancePlan)
                ->with('success', 'Plan de maintenance mis à jour');

        } catch (\Exception $e) {
            Log::error('Error updating maintenance plan', [
                'plan_id' => $maintenancePlan->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors('Une erreur est survenue lors de la mise à jour');
        }
    }

    /**
     * Supprime un plan de maintenance
     */
    public function destroy(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->delete();

        return redirect()
            ->route('maintenance-plans.index')
            ->with('success', 'Plan de maintenance supprimé');
    }

    /**
     * Génère les prochaines occurrences basées sur la RRULE
     * Cette logique peut être déléguée à un Job pour les performances
     */
    private function generateNextOccurrences(MaintenancePlan $plan)
    {
        try {
            $parser = new RruleParser($plan->rrule);
            $nextOccurrences = $parser->getNextOccurrences(now(), 12);

            // Vous pouvez sauvegarder les occurrences dans une table séparée
            // Pour les performances, on peut utiliser un cache
            // cache()->put("plan_{$plan->id}_occurrences", $nextOccurrences, now()->addDays(30));

            Log::info("Generated occurrences for plan {$plan->id}", [
                'count' => count($nextOccurrences),
            ]);

        } catch (\Exception $e) {
            Log::warning("Could not generate occurrences for plan {$plan->id}", [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
