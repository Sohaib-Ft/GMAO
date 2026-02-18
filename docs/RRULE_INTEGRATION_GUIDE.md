# Guide d'Intégration - Générateur RRULE

## 📋 Table des Matières
1. [Configuration Initiale](#configuration)
2. [Migrations](#migrations)
3. [Modèles](#modèles)
4. [Routes](#routes)
5. [Contrôleurs](#contrôleurs)
6. [Blade Views](#blade-views)
7. [Tests](#tests)
8. [Déploiement](#déploiement)

---

## Configuration

### Fichiers de Configuration

Le composant utilise trois fichiers de configuration:

1. **config/rrule.php** - Configuration des fréquences et jours
2. **app/Services/RruleParser.php** - Service de parsing
3. **resources/views/components/rrule-generator.blade.php** - Composant Vue

Aucune installation supplémentaire Composer n'est nécessaire.

---

## Migrations

### Migration pour la table `maintenance_plans`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')
                ->constrained('equipements')
                ->onDelete('cascade');
            
            // Colonne RRULE (la colonne principale)
            $table->text('rrule')
                ->comment('RFC 5545 RRULE format - ex: FREQ=WEEKLY;BYDAY=MO,WE,FR');
            
            // Colonnes facultatives pour compatibilité
            $table->integer('interval_jours')
                ->nullable()
                ->comment('Intervalle en jours (optionnel si RRULE est utilisée)');
            
            // Métadonnées
            $table->enum('type', ['preventive', 'corrective'])
                ->default('preventive');
            
            $table->enum('statut', ['actif', 'inactif'])
                ->default('actif');
            
            $table->foreignId('technicien_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            
            $table->text('description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performances
            $table->index('equipement_id');
            $table->index('statut');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
```

### Exécuter les migrations

```bash
php artisan migrate
```

---

## Modèles

### MaintenancePlan Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\RruleParser;

class MaintenancePlan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'equipement_id',
        'rrule',
        'interval_jours',
        'type',
        'statut',
        'technicien_id',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'equipement_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    // Accesseurs
    public function getRruleDetailsAttribute()
    {
        try {
            $parser = new RruleParser($this->rrule);
            return [
                'frequency' => $parser->getFrequency(),
                'interval' => $parser->getInterval(),
                'weekdays' => $parser->getWeekdays(),
                'day_of_month' => $parser->getDayOfMonth(),
                'french_summary' => $parser->toFrench(),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopePreventive($query)
    {
        return $query->where('type', 'preventive');
    }

    public function scopeForTechnician($query, $technicianId)
    {
        return $query->where('technicien_id', $technicianId);
    }
}
```

---

## Routes

### Routes/api.php

```php
// Optionnel: Endpoint API pour obtenir les détails RRULE
Route::get('/api/maintenance-plans/{id}/rrule-details', function ($id) {
    $plan = MaintenancePlan::findOrFail($id);
    return response()->json($plan->rrule_details);
});
```

### Routes/web.php

```php
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::resource('maintenance-plans', MaintenancePlanController::class, [
        'parameters' => ['maintenance_plan' => 'maintenancePlan']
    ]);
});
```

---

## Contrôleurs

### MaintenancePlanController

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenancePlanRequest;
use App\Http\Requests\UpdateMaintenancePlanRequest;
use App\Models\MaintenancePlan;
use App\Services\RruleParser;
use Illuminate\Support\Facades\Log;

class MaintenancePlanController extends Controller
{
    public function index()
    {
        $plans = MaintenancePlan::with('equipement', 'technicien')
            ->active()
            ->paginate(20);

        return view('admin.maintenance_plans.index', [
            'plans' => $plans,
        ]);
    }

    public function create()
    {
        $technicians = User::where('role', '!=', 'admin')->get();
        return view('admin.maintenance_plans.create', [
            'technicians' => $technicians,
        ]);
    }

    public function store(StoreMaintenancePlanRequest $request)
    {
        try {
            $validated = $request->validated();
            $plan = MaintenancePlan::create($validated);

            Log::info('Maintenance plan created', [
                'plan_id' => $plan->id,
                'rrule' => $plan->rrule,
            ]);

            return redirect()
                ->route('maintenance-plans.show', $plan)
                ->with('success', 'Plan de maintenance créé avec succès');
        } catch (\Exception $e) {
            Log::error('Error creating maintenance plan', [
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors('Une erreur est survenue');
        }
    }

    public function show(MaintenancePlan $maintenancePlan)
    {
        $parser = new RruleParser($maintenancePlan->rrule);

        return view('admin.maintenance_plans.show', [
            'plan' => $maintenancePlan,
            'rrule_summary' => $parser->toFrench(),
            'next_occurrences' => $parser->getNextOccurrences(now(), 12),
        ]);
    }

    public function edit(MaintenancePlan $maintenancePlan)
    {
        $technicians = User::where('role', '!=', 'admin')->get();

        return view('admin.maintenance_plans.edit', [
            'plan' => $maintenancePlan,
            'technicians' => $technicians,
        ]);
    }

    public function update(UpdateMaintenancePlanRequest $request, MaintenancePlan $maintenancePlan)
    {
        try {
            $validated = $request->validated();
            $maintenancePlan->update($validated);

            return redirect()
                ->route('maintenance-plans.show', $maintenancePlan)
                ->with('success', 'Plan mis à jour avec succès');
        } catch (\Exception $e) {
            Log::error('Error updating maintenance plan', [
                'plan_id' => $maintenancePlan->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors('Une erreur est survenue');
        }
    }

    public function destroy(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->delete();

        return redirect()
            ->route('maintenance-plans.index')
            ->with('success', 'Plan supprimé');
    }
}
```

---

## Blade Views

### resources/views/admin/maintenance_plans/create.blade.php

```blade
@extends('layouts.app')

@section('title', 'Créer un Plan de Maintenance')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('maintenance-plans.store') }}" class="space-y-8">
        @csrf

        <!-- Équipement -->
        <div>
            <label class="block text-sm font-bold mb-2">Équipement *</label>
            <select name="equipement_id" class="w-full rounded-lg border-gray-300">
                <option>— Sélectionnez un équipement —</option>
                @foreach($equipements as $eq)
                    <option value="{{ $eq->id }}">{{ $eq->nom }}</option>
                @endforeach
            </select>
            @error('equipement_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- Composant RRULE Generator -->
        <x-rrule-generator 
            name="rrule"
            :value="old('rrule')"
            label="Règle de Récurrence"
            :required="true"
        />
        @error('rrule')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror

        <!-- Technicien -->
        <div>
            <label class="block text-sm font-bold mb-2">Technicien Assigné</label>
            <select name="technicien_id" class="w-full rounded-lg border-gray-300">
                <option>— Aucun —</option>
                @foreach($technicians as $tech)
                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('maintenance-plans.index') }}" 
               class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg">
                Créer
            </button>
        </div>
    </form>
</div>
@endsection
```

---

## Tests

### Exécuter les tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques au RRULE
php artisan test tests/Unit/RruleParserTest.php

# Ou utiliser PHPUnit directement
./vendor/bin/phpunit tests/Unit/RruleParserTest.php
```

---

## Déploiement

### Checklist de Déploiement

- [ ] Fichier `config/rrule.php` présent
- [ ] Fichier `app/Services/RruleParser.php` présent
- [ ] Composant Blade `resources/views/components/rrule-generator.blade.php` présent
- [ ] Migration exécutée (`php artisan migrate`)
- [ ] Modèle `MaintenancePlan` créé/mis à jour
- [ ] Contrôleur `MaintenancePlanController` créé/mis à jour
- [ ] Routes configurées
- [ ] Tests passent (`php artisan test`)
- [ ] Assets Tailwind compilés (`npm run build` ou `npm run dev`)
- [ ] Alpine.js chargé dans le layout

### Vérifier Alpine.js

Assurez-vous que Alpine.js 3+ est chargé dans votre layout:

```blade
<!-- Dans resources/views/layouts/app.blade.php -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
```

Ou si installé localement:

```blade
@vite(['resources/js/app.js'])
```

---

## Troubleshooting

### Le composant n'apparaît pas

1. Vérifier que Alpine.js est chargé: `console.log(window.Alpine)`
2. Vérifier que le composant est dans le bon dossier
3. Vérifier la syntaxe des Blade components: `<x-rrule-generator />`

### RRULE n'est pas généré

1. Vérifier le formulaire contient `<input type="hidden" name="rrule">`
2. Vérifier Alpine.js est démarré: `Alpine.entangle()`
3. Vérifier la console du navigateur pour les erreurs

### Validation échoue

1. Vérifier la RRULE commence par `FREQ=`
2. Vérifier toutes les valeurs sont séparées par `;`
3. Utiliser `RruleParser::isValidRrule()` pour tester

---

## Exemples Avancés

### Générer des Notifications Basées sur la RRULE

```php
// app/Console/Commands/SendMaintenanceNotifications.php
public function handle()
{
    $plans = MaintenancePlan::where('statut', 'actif')->get();

    foreach ($plans as $plan) {
        $parser = new RruleParser($plan->rrule);
        $nextOccurrence = $parser->getNextOccurrences(now(), 1)[0];

        if ($nextOccurrence->isPassed()) {
            Notification::send($plan->technicien, new MaintenanceDueNotification($plan));
        }
    }
}
```

### Export RRULE vers iCalendar

```php
// Générer un fichier ICS
$parser = new RruleParser($plan->rrule);

$ical = "BEGIN:VCALENDAR\r\n";
$ical .= "VERSION:2.0\r\n";
$ical .= "BEGIN:VEVENT\r\n";
$ical .= "UID:" . $plan->id . "@cmms.local\r\n";
$ical .= "DTSTART:" . now()->format('Ymd\THis\Z') . "\r\n";
$ical .= "SUMMARY:Maintenance - " . $plan->equipement->nom . "\r\n";
$ical .= "RRULE:" . $plan->rrule . "\r\n";
$ical .= "END:VEVENT\r\n";
$ical .= "END:VCALENDAR\r\n";
```

---

## Ressources

- [RFC 5545 - iCalendar Format](https://tools.ietf.org/html/rfc5545#section-3.6.5)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Laravel Blade Components](https://laravel.com/docs/blade#components)

---

## Support

Pour les problèmes ou questions:
1. Vérifier la documentation RRULE_GENERATOR.md
2. Consulter les tests: `tests/Unit/RruleParserTest.php`
3. Vérifier les logs: `storage/logs/laravel.log`
