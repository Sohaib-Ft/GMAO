<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;
use App\Services\RruleParser;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TechnicianDashboardController extends Controller
{
    /**
     * Affiche le dashboard du technicien avec les maintenances planifiées
     */
    public function index(Request $request)
    {
        $technician = auth()->user();

        // Récupérer les plans de maintenance actifs
        $plans = MaintenancePlan::where('active', true)
            ->with(['equipement.localisation'])
            ->get();

        // Générer les occurrences pour les 30 prochains jours
        $upcomingMaintenance = $this->getUpcomingMaintenanceForTechnician($plans, $technician);

        // Grouper par date
        $maintenanceByDate = $upcomingMaintenance->groupBy(function($item) {
            return $item['date']->format('Y-m-d');
        })->sortKeys();

        // Pour la semaine prochaine
        $weekMaintenance = $upcomingMaintenance->filter(function($item) {
            return $item['date'] <= now()->addDays(7);
        })->values();

        // Statistiques
        $stats = [
            'total_plans' => $plans->count(),
            'this_week' => $weekMaintenance->count(),
            'next_30_days' => $upcomingMaintenance->count(),
        ];

        return view('technician.dashboard', [
            'maintenance' => $upcomingMaintenance,
            'weekMaintenance' => $weekMaintenance,
            'maintenanceByDate' => $maintenanceByDate,
            'stats' => $stats,
        ]);
    }

    /**
     * Récupère les maintenances à venir pour le technicien
     */
    private function getUpcomingMaintenanceForTechnician($plans, $technician)
    {
        $maintenance = collect();
        $now = now();
        $endDate = $now->copy()->addDays(30);

        foreach ($plans as $plan) {
            // Vérifier si RRULE existe
            if (!$plan->rrule) {
                continue;
            }

            try {
                // Générer les occurrences
                $parser = new RruleParser($plan->rrule);
                $occurrences = $parser->getNextOccurrences($now, 100);

                // Ajouter les occurrences dans la plage
                foreach ($occurrences as $date) {
                    if ($date <= $endDate) {
                        $maintenance->push([
                            'plan' => $plan,
                            'date' => $date,
                            'type' => 'maintenance',
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Ignorer les RRULE invalides
                continue;
            }
        }

        return $maintenance->sortBy(function($item) {
            return $item['date']->timestamp;
        })->values();
    }
}
