<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the technical team.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'technicien')
            ->withCount(['interventions' => function($query) {
                $query->whereNotNull('date_fin');
            }]);

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $technicians = $query->get();
        $viewMode = $request->get('view_mode', 'grid');

        if ($request->ajax()) {
            return view('technician.team._team_list', compact('technicians', 'viewMode'));
        }

        return view('technician.team.index', compact('technicians', 'viewMode'));
    }

    /**
     * Display a technician's profile.
     */
    public function show(User $user)
    {
        if ($user->role !== 'technicien') {
            abort(404);
        }

        $user->loadCount(['interventions' => function($query) {
            $query->whereNotNull('date_fin');
        }]);

        // Get some recent work orders assigned to this technician
        $recentTasks = WorkOrder::where('technicien_id', $user->id)
            ->with('equipement')
            ->latest()
            ->take(5)
            ->get();

        return view('technician.team.show', compact('user', 'recentTasks'));
    }
}
