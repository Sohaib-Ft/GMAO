<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Localisation;

class LocalisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Localisation::withCount('equipements')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $localisations = $query->get();
        return view('admin.localisations.index', compact('localisations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:localisations,name|max:255',
        ]);

        Localisation::create($validated);

        return redirect()->route('localisations.index')->with('success', 'Site ajouté avec succès.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Localisation $localisation)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:localisations,name,' . $localisation->id . '|max:255',
        ]);

        $localisation->update($validated);

        return redirect()->route('localisations.index')->with('success', 'Site mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Localisation $localisation)
    {
        if ($localisation->equipements()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce site car il est lié à des équipements.');
        }

        $localisation->delete();

        return redirect()->route('localisations.index')->with('success', 'Site supprimé avec succès.');
    }
}
