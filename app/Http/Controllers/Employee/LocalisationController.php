<?php

namespace App\Http\Controllers\Employee;

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
        return view('employee.localisations.index', compact('localisations'));
    }

    public function show(Localisation $localisation)
    {
        $localisation->loadCount('equipements');
        return view('employee.localisations.show', compact('localisation'));
    }
}
