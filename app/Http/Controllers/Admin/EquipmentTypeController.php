<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EquipmentType;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EquipmentType::withCount('equipements')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $types = $query->get();
        return view('admin.equipment_types.index', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:equipment_types,name|max:255',
        ]);

        EquipmentType::create($validated);

        return redirect()->route('equipment_types.index')->with('success', 'Type d\'équipement ajouté avec succès.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EquipmentType $equipmentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:equipment_types,name,' . $equipmentType->id . '|max:255',
        ]);

        $equipmentType->update($validated);

        return redirect()->route('equipment_types.index')->with('success', 'Type d\'équipement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EquipmentType $equipmentType)
    {
        if ($equipmentType->equipements()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce type car il est lié à des équipements.');
        }

        $equipmentType->delete();

        return redirect()->route('equipment_types.index')->with('success', 'Type d\'équipement supprimé avec succès.');
    }
}
