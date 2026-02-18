<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of equipment types (read-only for employees).
     */
    public function index(Request $request)
    {
        $query = EquipmentType::withCount('equipements');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $types = $query->orderBy('name')->get();

        return view('employee.equipement_types.index', compact('types'));
    }
}
