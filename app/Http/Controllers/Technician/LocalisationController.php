<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Localisation;

class LocalisationController extends Controller
{
    public function index(Request $request)
    {
        $query = Localisation::withCount('equipements')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $localisations = $query->get();
        return view('technician.localisations.index', compact('localisations'));
    }

    public function show(Localisation $localisation)
    {
        $localisation->loadCount('equipements');
        return view('technician.localisations.show', compact('localisation'));
    }
}
