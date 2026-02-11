<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Equipement;
use App\Models\WorkOrder;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques
        $equipments_count = Equipement::count();
        $critical_failures = WorkOrder::where('priorite', 'urgente')->where('statut', '!=', 'terminee')->count();
        
        $availability_rate = $equipments_count > 0 
            ? round((($equipments_count - $critical_failures) / $equipments_count) * 100, 1)
            : 100;
        
        $stats = [
            'total_users' => User::count(),
            'technicians_count' => User::where('role', 'technicien')->count(),
            'standard_users_count' => User::where('role', 'employe')->count(),
            'equipments_count' => $equipments_count,
            'active_technicians' => User::where('role', 'technicien')->where('is_active', true)->count(),
            'pending_requests' => WorkOrder::where('statut', 'en_attente')->count(),
            'tasks_in_progress' => WorkOrder::where('statut', 'en_cours')->count(),
            'completed_interventions' => WorkOrder::where('statut', 'terminee')->count(),
            'critical_failures' => $critical_failures,
            'availability_rate' => $availability_rate,
            'most_claimed_equipment' => Equipement::withCount('workOrders')
                ->orderBy('work_orders_count', 'desc')
                ->first(),
            'top_claimant' => User::where('role', 'employe')
                ->withCount('employeWorkOrders')
                ->orderBy('employe_work_orders_count', 'desc')
                ->first(),
            'top_technician' => User::where('role', 'technicien')
                ->withCount('technicienWorkOrders')
                ->orderBy('technicien_work_orders_count', 'desc')
                ->first(),
        ];

        // Dernières demandes de maintenance
        $recentRequests = WorkOrder::with(['employe', 'equipement', 'technicien'])
            ->latest()
            ->take(5)
            ->get();

        // Données pour le graphique (Interventions par mois)
        $monthlyCounts = WorkOrder::select(
                DB::raw('COUNT(*) as count'), 
                DB::raw('MONTH(created_at) as month_num')
            )
            ->where('statut', 'terminee')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->pluck('count', 'month_num');

        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];

        $monthlyInterventions = [];
        foreach ($months as $num => $name) {
            $monthlyInterventions[] = [
                'month' => $name,
                'count' => $monthlyCounts->get($num, 0)
            ];
        }

        // Répartition des statuts
        $statusDistribution = WorkOrder::select('statut', DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get();

        return view('dashboards.admin', compact('stats', 'recentRequests', 'monthlyInterventions', 'statusDistribution'));
    }
}
