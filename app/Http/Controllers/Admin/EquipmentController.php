<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipement;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Localisation;
use App\Models\EquipmentType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipement::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhereHas('equipmentType', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('localisationRelation', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filter by site
        if ($request->filled('site')) {
            $query->bySite($request->site);
        }

        $equipments = $query->latest()->paginate(10);
        $sites = Localisation::all();

        return view('admin.equipments.index', compact('equipments', 'sites'));
    }

    /** 
     * Show import form.
     */
    public function showImport()
    {
        return view('admin.equipments.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        
        if ($handle === false) {
            return back()->with('error', 'Impossible d\'ouvrir le fichier.');
        }

        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'Le fichier CSV est vide ou mal formaté.');
        }
        
        // Nettoyage des headers (suppression des espaces, BOM, etc.)
        $header = array_map(function($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0] ?? $h)));
        }, $header);

        $count = 0;
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $rowNumber++;
            
            // Ignorer les lignes vides
            if (empty(array_filter($row))) continue;

            $data = array_combine(array_slice($header, 0, count($row)), $row);
            
            // Validation basique
            $nom = trim($data['nom'] ?? $data['name'] ?? '');
            $code = trim($data['code'] ?? '');

            if (!$nom || !$code) {
                $errors[] = "Ligne $rowNumber : Le nom et le code sont obligatoires.";
                continue;
            }

            try {
                \App\Models\Equipement::updateOrCreate(
                    ['code' => $code],
                    [
                        'nom' => $nom,
                        'marque' => trim($data['marque'] ?? $data['brand'] ?? null),
                        'modele' => trim($data['modele'] ?? $data['model'] ?? null),
                        'numero_serie' => trim($data['numero_serie'] ?? $data['serial'] ?? null),
                        'statut' => trim($data['statut'] ?? $data['status'] ?? 'inactif'),
                        'localisation' => trim($data['localisation'] ?? $data['location'] ?? null),
                        'notes' => trim($data['notes'] ?? null),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $errors[] = "Ligne $rowNumber : " . $e->getMessage();
            }
        }

        fclose($handle);

        if (count($errors) > 0) {
            $message = "$count équipements importés. Des erreurs sont survenues :\n" . implode("\n", array_slice($errors, 0, 5));
            if (count($errors) > 5) $message .= "\n... (et " . (count($errors) - 5) . " autres erreurs)";
            return redirect()->route('equipments.index')->with('status', $message);
        }

        return redirect()->route('equipments.index')->with('status', "$count équipements importés avec succès.");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sites = Localisation::all();
        $types = EquipmentType::all();
        return view('admin.equipments.create', compact('sites', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'nullable|string|unique:equipements,code|max:50',
            'numero_serie' => 'nullable|string|max:100',
            'marque' => 'nullable|string|max:100',
            'modele' => 'nullable|string|max:100',
            'statut' => 'required|in:actif,inactif,maintenance,panne',
            'site' => 'nullable|string|max:255',
            'departement' => 'nullable|string|max:100',
            'date_installation' => 'nullable|date',
            'annee_fabrication' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'date_fin_garantie' => 'nullable|date',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
            'manuel' => 'nullable|file|mimes:pdf|max:10240', // 10MB Max
            'type_equipement' => 'required|string|max:100',
        ]);

        // Handle File Uploads
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('equipments/images', 'public');
        }

        if ($request->hasFile('manuel')) {
            $validated['manuel_path'] = $request->file('manuel')->store('equipments/manuels', 'public');
        }

        // If code is empty (form submits an empty string), remove it so model can auto-generate
        if (array_key_exists('code', $validated) && empty($validated['code'])) {
            unset($validated['code']);
        }

        Equipement::create($validated);

        return redirect()->route('equipments.index')->with('status', 'Équipement créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipement $equipment)
    {
        return view('admin.equipments.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipement $equipment)
    {
        $sites = Localisation::all();
        $types = EquipmentType::all();
        return view('admin.equipments.edit', compact('equipment', 'sites', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipement $equipment)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:equipements,code,' . $equipment->id,
            'numero_serie' => 'nullable|string|max:100',
            'marque' => 'nullable|string|max:100',
            'modele' => 'nullable|string|max:100',
            'statut' => 'required|in:actif,inactif,maintenance,panne',
            'site' => 'nullable|string|max:100',
            'departement' => 'nullable|string|max:100',
            'type_equipement' => 'nullable|string|max:100',
            'date_installation' => 'nullable|date',
            'annee_fabrication' => 'nullable|integer',
            'annee_acquisition' => 'nullable|integer',
            'date_fin_garantie' => 'nullable|date',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'manuel' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($equipment->image_path) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('equipments/images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($equipment->image_path) {
                Storage::disk('public')->delete($equipment->image_path);
            }
            $validated['image_path'] = null;
        }

        if ($request->hasFile('manuel')) {
            // Delete old manual
            if ($equipment->manuel_path) {
                Storage::disk('public')->delete($equipment->manuel_path);
            }
            $validated['manuel_path'] = $request->file('manuel')->store('equipments/manuels', 'public');
        } elseif ($request->boolean('remove_manuel')) {
            if ($equipment->manuel_path) {
                Storage::disk('public')->delete($equipment->manuel_path);
            }
            $validated['manuel_path'] = null;
        }

        $equipment->update($validated);

        \Log::info('Equipment updated, redirecting to index');

        return redirect()->route('equipments.index')->with('status', 'Équipement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipement $equipment)
    {
        if ($equipment->image_path) {
            Storage::disk('public')->delete($equipment->image_path);
        }
        if ($equipment->manuel_path) {
            Storage::disk('public')->delete($equipment->manuel_path);
        }
        
        $equipment->delete();

        return redirect()->route('equipments.index')->with('status', 'Équipement supprimé avec succès.');
    }
}

