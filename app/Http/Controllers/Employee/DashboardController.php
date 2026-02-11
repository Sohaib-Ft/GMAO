<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistiques pour l'employé
        $totalWorkOrders = WorkOrder::where('employe_id', $user->id)->count();
        $pendingWorkOrders = WorkOrder::where('employe_id', $user->id)
            ->whereIn('statut', ['nouvelle', 'en_attente'])
            ->count();
        $inProgressWorkOrders = WorkOrder::where('employe_id', $user->id)
            ->where('statut', 'en_cours')
            ->count();
        $completedWorkOrders = WorkOrder::where('employe_id', $user->id)
            ->where('statut', 'terminee')
            ->count();

        // Demandes récentes
        $recentWorkOrders = WorkOrder::where('employe_id', $user->id)
            ->with(['technicien', 'equipement'])
            ->latest()
            ->limit(5)
            ->get();

        // Données pour le graphique (Demandes par mois)
        $monthlyCounts = WorkOrder::select(
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), 
                \Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month_num')
            )
            ->where('employe_id', $user->id)
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->pluck('count', 'month_num');

        $months = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juil', 8 => 'Août',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];

        $monthlyData = [];
        foreach ($months as $num => $name) {
            $monthlyData[] = [
                'month' => $name,
                'count' => $monthlyCounts->get($num, 0)
            ];
        }

        return view('dashboards.employe', compact(
            'totalWorkOrders',
            'pendingWorkOrders',
            'inProgressWorkOrders',
            'completedWorkOrders',
            'recentWorkOrders',
            'monthlyData'
        ));
    }
}
