<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Toutes les tâches liées au technicien
        $allMyWorkOrders = WorkOrder::where('technicien_id', $user->id)->get();
        
        // Tâche en cours (il ne devrait y en avoir qu'une normalement)
        $currentTask = WorkOrder::with(['equipement', 'employe'])
            ->where('technicien_id', $user->id)
            ->where('statut', 'en_cours')
            ->first();

        // Tâches à faire (affectées mais pas commencées)
        $todoTasks = WorkOrder::with(['equipement', 'employe'])
            ->where('technicien_id', $user->id)
            ->where('statut', 'en_attente') // Dans le nouveau système, affectée = en_attente avec technicien_id
            ->latest()
            ->get();

        // Tâches disponibles (personne n'est assigné)
        $availableTasksCount = WorkOrder::whereNull('technicien_id')
            ->where('statut', 'en_attente')
            ->count();

        // Statistiques
        $stats = [
            'todo_count' => $todoTasks->count(),
            'in_progress_count' => $allMyWorkOrders->where('statut', 'en_cours')->count(),
            'completed_count' => $allMyWorkOrders->where('statut', 'terminee')->count(),
            'urgent_count' => $allMyWorkOrders->where('priorite', 'urgente')->where('statut', '!=', 'terminee')->count(),
            'available_count' => $availableTasksCount,
        ];

        // Statistiques mensuelles pour l'année en cours
        $monthlyCounts = WorkOrder::select(
                \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'), 
                \Illuminate\Support\Facades\DB::raw('MONTH(created_at) as month_num')
            )
            ->where('technicien_id', $user->id)
            ->where('statut', 'terminee')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_num')
            ->orderBy('month_num')
            ->get()
            ->pluck('count', 'month_num');

        $months = [
            1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
            5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Aoû',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'
        ];

        $monthlyInterventions = [];
        foreach ($months as $num => $name) {
            $monthlyInterventions[] = [
                'month' => $name,
                'count' => $monthlyCounts->get($num, 0)
            ];
        }

        // Dernières activités ou messages
        $recentMessages = Message::where('receiver_id', $user->id)
            ->with('sender')
            ->latest()
            ->take(3)
            ->get();

        // Total annuel des interventions terminées pour ce technicien
        $annualTotal = WorkOrder::where('technicien_id', $user->id)
            ->where('statut', 'terminee')
            ->whereYear('created_at', date('Y'))
            ->count();

        // Durée moyenne des tâches (en secondes) puis formatage
        $avgSeconds = WorkOrder::where('technicien_id', $user->id)
            ->where('statut', 'terminee')
            ->whereNotNull('date_debut')
            ->whereNotNull('date_fin')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, date_debut, date_fin)'));

        if ($avgSeconds && $avgSeconds > 0) {
            $hours = floor($avgSeconds / 3600);
            $minutes = floor(($avgSeconds % 3600) / 60);
            $avgTaskDuration = ($hours > 0 ? $hours . 'h ' : '') . $minutes . 'm';
        } else {
            $avgTaskDuration = '—';
        }

        return view('dashboards.technicien', compact('stats', 'currentTask', 'todoTasks', 'recentMessages', 'monthlyInterventions', 'annualTotal', 'avgTaskDuration'));
    }
}
