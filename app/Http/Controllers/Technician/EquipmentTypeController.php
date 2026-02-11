<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of equipment types (read-only for technicians).
     */
    public function index(Request $request)
    {
        $query = EquipmentType::withCount('equipements');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $types = $query->orderBy('name')->get();

        return view('technician.equipement_types.index', compact('types'));
    }
}
