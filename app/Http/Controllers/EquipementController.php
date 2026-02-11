<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Services\AssetCodeGenerator;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    /**
     * Display a listing of the equipements.
     */
    public function index(Request $request)
    {
        $query = Equipement::with('responsable');

        // Filtres
        if ($request->filled('departement')) {
            $query->byDepartement($request->departement);
        }

        if ($request->filled('site')) {
            $query->bySite($request->site);
        }

        if ($request->filled('categorie')) {
            $query->byCategorie($request->categorie);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('numero_serie', 'LIKE', "%{$search}%");
            });
        }

        $equipements = $query->orderBy('code')->paginate(20);

        // Obtenir les listes pour les filtres
        $departements = Equipement::select('departement')->distinct()->pluck('departement');
        $sites = Equipement::select('site')->distinct()->pluck('site');
        $categories = Equipement::select('categorie')->distinct()->pluck('categorie');

        return view('equipements.index', compact(
            'equipements',
            'departements',
            'sites',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new equipement.
     */
    public function create()
    {
        // Récupérer les codes disponibles depuis le service
        $categoryCodes = AssetCodeGenerator::getAvailableCategoryCodes();
        $departmentCodes = AssetCodeGenerator::getAvailableDepartmentCodes();
        $siteCodes = AssetCodeGenerator::getAvailableSiteCodes();

        return view('equipements.create', compact(
            'categoryCodes',
            'departmentCodes',
            'siteCodes'
        ));
    }

    /**
     * Store a newly created equipement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie' => 'required|string|max:50',
            'type_equipement' => 'nullable|string|max:50',
            'departement' => 'required|string|max:50',
            'site' => 'required|string|max:50',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'annee_acquisition' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'annee_fabrication' => 'nullable|integer|min:1900|max:' . date('Y'),
            'date_installation' => 'nullable|date',
            'date_fin_garantie' => 'nullable|date',
            'statut' => 'required|in:actif,inactif,maintenance,panne',
            'localisation' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Le code sera généré automatiquement par le modèle
        $equipement = Equipement::create($validated);

        return redirect()
            ->route('equipements.show', $equipement)
            ->with('success', "Équipement créé avec succès. Code: {$equipement->code}");
    }

    /**
     * Display the specified equipement.
     */
    public function show(Equipement $equipement)
    {
        $equipement->load(['responsable', 'workOrders']);

        return view('equipements.show', compact('equipement'));
    }

    /**
     * Show the form for editing the specified equipement.
     */
    public function edit(Equipement $equipement)
    {
        $categoryCodes = AssetCodeGenerator::getAvailableCategoryCodes();
        $departmentCodes = AssetCodeGenerator::getAvailableDepartmentCodes();
        $siteCodes = AssetCodeGenerator::getAvailableSiteCodes();

        return view('equipements.edit', compact(
            'equipement',
            'categoryCodes',
            'departmentCodes',
            'siteCodes'
        ));
    }

    /**
     * Update the specified equipement in storage.
     */
    public function update(Request $request, Equipement $equipement)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'categorie' => 'required|string|max:50',
            'type_equipement' => 'nullable|string|max:50',
            'departement' => 'required|string|max:50',
            'site' => 'required|string|max:50',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'annee_acquisition' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'annee_fabrication' => 'nullable|integer|min:1900|max:' . date('Y'),
            'date_installation' => 'nullable|date',
            'date_fin_garantie' => 'nullable|date',
            'statut' => 'required|in:actif,inactif,maintenance,panne',
            'localisation' => 'nullable|string|max:255',
            'responsable_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Si la catégorie, le département ou le site change, régénérer le code
        if (
            $equipement->categorie !== $validated['categorie'] ||
            $equipement->departement !== $validated['departement'] ||
            $equipement->site !== $validated['site']
        ) {
            $generator = new AssetCodeGenerator();
            $validated['code'] = $generator->generate((object) $validated);
        }

        $equipement->update($validated);

        return redirect()
            ->route('equipements.show', $equipement)
            ->with('success', 'Équipement mis à jour avec succès.');
    }

    /**
     * Remove the specified equipement from storage.
     */
    public function destroy(Equipement $equipement)
    {
        $code = $equipement->code;
        $equipement->delete();

        return redirect()
            ->route('equipements.index')
            ->with('success', "Équipement {$code} supprimé avec succès.");
    }

    /**
     * API: Valider un code d'équipement
     */
    public function validateCode(Request $request)
    {
        $code = $request->input('code');
        $generator = new AssetCodeGenerator();
        
        $isValid = $generator->validate($code);
        $exists = Equipement::where('code', $code)->exists();

        return response()->json([
            'valid' => $isValid,
            'exists' => $exists,
            'message' => !$isValid 
                ? 'Format de code invalide' 
                : ($exists ? 'Ce code existe déjà' : 'Code valide et disponible')
        ]);
    }

    /**
     * API: Prévisualiser le code avant création
     */
    public function previewCode(Request $request)
    {
        $generator = new AssetCodeGenerator();
        
        // Créer un objet temporaire pour la prévisualisation
        $tempEquipement = (object) [
            'categorie' => $request->input('categorie'),
            'departement' => $request->input('departement'),
            'site' => $request->input('site'),
            'annee_acquisition' => $request->input('annee_acquisition'),
        ];

        try {
            $code = $generator->generate($tempEquipement);
            
            return response()->json([
                'success' => true,
                'code' => $code,
                'message' => "Code prévu: {$code}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer le code'
            ], 400);
        }
    }
}
