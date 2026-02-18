# Générateur de Récurrence RRULE (RFC 5545)

## Vue d'ensemble

Le composant `rrule-generator` est un interface moderne et intuitive pour générer des règles de récurrence compatibles avec la RFC 5545 (iCalendar). Il permet aux utilisateurs de créer des règles de récurrence sans connaître la syntaxe technique.

## Caractéristiques

### 1. **Sélecteur de Fréquence**
- 🌞 **Quotidien** : Se répète chaque jour
- 📅 **Hebdomadaire** : Se répète chaque semaine avec sélection des jours
- 📆 **Mensuel** : Se répète chaque mois (2 options : jour fixe ou jour relatif)
- 📊 **Annuel** : Se répète chaque année

### 2. **Configuration Dynamique**

#### Hebdomadaire
- Affiche 7 boutons pour sélectionner les jours (L, M, M, J, V, S, D)
- Sélection multiple
- Affiche un lundi seulement (raccourci)

#### Mensuel
- **Option 1** : Le [X] du mois (ex: Le 15 de chaque mois)
- **Option 2** : Le [Position] [Jour] du mois (ex: Le deuxième lundi)

### 3. **Intervalle**
- Permet de spécifier "tous les N [unité]"
- Exemple: tous les 2 semaines, tous les 3 mois

### 4. **Résumé en Temps Réel**
Génère une phrase en français qui traduit dynamiquement la règle:
- "Se répète le lundi et vendredi"
- "Se répète tous les 2 semaines le mardi"
- "Se répète le 15 de chaque mois"
- "Se répète le deuxième lundi de chaque mois"

### 5. **RRULE RFC 5545**
Génère automatiquement la chaîne RRULE conforme à la RFC 5545:
- `FREQ=DAILY`
- `FREQ=WEEKLY;BYDAY=MO,WE,FR`
- `FREQ=MONTHLY;BYMONTHDAY=15`
- `FREQ=MONTHLY;BYDAY=2MO` (deuxième lundi)

## Utilisation

### Dans une Blade View

```blade
<x-rrule-generator 
    name="rrule" 
    :value="old('rrule', '')"
    label="Règle de Récurrence (RRULE)"
    :required="true"
/>
```

### Propriétés

| Propriété | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `name` | string | `rrule` | Nom du champ hidden pour l'input |
| `value` | string | `''` | Valeur RRULE initiale (pour édition) |
| `label` | string | `Règle de Récurrence (RRULE)` | Texte du label |
| `required` | boolean | `true` | Si le champ est requis |

### Stockage dans la Base de Données

```php
// Dans votre migration
Schema::create('maintenance_plans', function (Blueprint $table) {
    $table->id();
    // ... autres colonnes
    $table->text('rrule')->nullable()->comment('RFC 5545 RRULE format');
    $table->timestamps();
});
```

### Validation

```php
// Dans votre FormRequest ou contrôleur
public function rules()
{
    return [
        'rrule' => 'required|string|regex:/^FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)/',
        // ... autres validations
    ];
}
```

## Exemples de Sortie RRULE

### Quotidien
```
FREQ=DAILY
FREQ=DAILY;INTERVAL=2  // Tous les 2 jours
```

### Hebdomadaire
```
FREQ=WEEKLY;BYDAY=MO,WE,FR
FREQ=WEEKLY;INTERVAL=2;BYDAY=TU,TH  // Tous les 2 semaines, mardi et jeudi
```

### Mensuel
```
FREQ=MONTHLY;BYMONTHDAY=15  // Le 15 de chaque mois
FREQ=MONTHLY;BYDAY=2MO       // Le deuxième lundi de chaque mois
FREQ=MONTHLY;BYDAY=-1FR      // Le dernier vendredi de chaque mois
```

### Annuel
```
FREQ=YEARLY
FREQ=YEARLY;INTERVAL=2  // Tous les 2 ans
```

## Architecture

### Stack Technique
- **Alpine.js** : Réactivité et logique côté client
- **Tailwind CSS** : Design minimaliste et responsive
- **Blade Components** : Composant Laravel réutilisable
- **Lucide/Blade Icons** : Icônes visuelles (via Boxicons)

### Structure du Composant

```
rrule-generator/
├── Sélecteur de Fréquence (4 boutons)
├── Configuration Intervalle
├── Configuration Dynamique (selon fréquence)
│   ├── Weekly: Sélecteur de jours
│   ├── Monthly: Jour fixe ou relatif
│   └── Yearly: Info seulement
├── Résumé en français
└── Champ caché + Copy button
```

### Données Alpine.js

```javascript
{
    frequency: 'WEEKLY',           // DAILY, WEEKLY, MONTHLY, YEARLY
    interval: 1,                   // Intervalle (1-99)
    weekdays: ['MO', 'WE', 'FR'],  // Pour WEEKLY
    monthlyType: 'dayOfMonth',     // dayOfMonth ou relativeDay
    dayOfMonth: 15,                // Pour MONTHLY (option 1)
    monthlyPosition: '2',          // 1-4 ou -1 (dernier)
    monthlyDay: 'MO',              // MO-SU pour MONTHLY (option 2)
}
```

## Intégration avec le Modèle

### Modèle MaintenancePlan

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    protected $fillable = [
        'equipement_id',
        'rrule',
        'interval_jours',
        'technicien_id',
        'type',
        'statut',
        'description',
    ];

    // Accesseur pour parser la RRULE
    public function getRruleDetailsAttribute()
    {
        $details = [];
        $parts = explode(';', $this->rrule);
        
        foreach ($parts as $part) {
            [$key, $value] = explode('=', $part);
            $details[$key] = $value;
        }
        
        return $details;
    }

    // Pour générer les prochaines occurrences
    public function getNextOccurrences($count = 10)
    {
        // Utiliser une librairie comme: roshambo/icalendar or mmarabotto/rrule
        // ou implémenter une logique custom
    }
}
```

### Contrôleur

```php
<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;

class MaintenancePlanController extends Controller
{
    public function store(StoreMaintenancePlanRequest $request)
    {
        $validated = $request->validated();
        
        MaintenancePlan::create($validated);
        
        return redirect()->route('maintenance-plans.index')
            ->with('success', 'Plan de maintenance créé avec succès');
    }
}
```

## Validation en Backend

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenancePlanRequest extends FormRequest
{
    public function rules()
    {
        return [
            'equipement_id' => 'required|exists:equipements,id',
            'rrule' => [
                'required',
                'string',
                'regex:/^FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)/',
                function ($attribute, $value, $fail) {
                    // Validation personnalisée si nécessaire
                    if (str_contains($value, 'BYDAY') && str_starts_with($value, 'FREQ=WEEKLY')) {
                        // WEEKLY doit avoir BYDAY
                    }
                }
            ],
            'interval_jours' => 'nullable|integer|min:1|max:3650',
            'technicien_id' => 'nullable|exists:users,id',
        ];
    }
}
```

## Exemples d'Utilisation

### Maintenance Hebdomadaire
**Cas d'usage** : Nettoyage des équipements
```
FREQ=WEEKLY;BYDAY=MO,WE,FR
Résumé: "Se répète le lundi, mercredi et vendredi"
```

### Maintenance Mensuelle (jour fixe)
**Cas d'usage** : Révision complète
```
FREQ=MONTHLY;BYMONTHDAY=1
Résumé: "Se répète le 1er de chaque mois"
```

### Maintenance Mensuelle (jour relatif)
**Cas d'usage** : Service spécialisé
```
FREQ=MONTHLY;BYDAY=-1FR
Résumé: "Se répète le dernier vendredi de chaque mois"
```

### Maintenance Trimestrale
**Cas d'usage** : Inspection approfondie
```
FREQ=MONTHLY;INTERVAL=3;BYMONTHDAY=15
Résumé: "Se répète tous les 3 mois le 15"
```

## Personnalisation

### Changer les couleurs
Modifier les classes Tailwind dans le composant:
```blade
:class="frequency === 'DAILY' 
    ? 'bg-indigo-600 text-white border-indigo-600' 
    : 'bg-gray-50 text-gray-700 border-gray-200'"
```

### Ajouter des contraintes
```javascript
// Dans le composant Alpine.js
init() {
    // Votre logique personnalisée
}
```

### Traductions multilingues
```blade
<span x-text="frequency === 'DAILY' 
    ? '{{ __('Quotidien') }}' 
    : ''"></span>
```

## Dépannage

### RRULE non généré
- Vérifier que le champ hidden `name="rrule"` est présent
- Vérifier la console Alpine.js : `Alpine.entangle()`

### Fréquence non mise à jour
- Vérifier que Alpine.js est chargé (`@{{ }}` au lieu de `{{ }}`)
- Vérifier la version de Tailwind (requiert v3+)

### Validation côté serveur échoue
- Vérifier que la RRULE commence par `FREQ=`
- Vérifier que la valeur d'intervalle est un nombre valide

## Références

- [RFC 5545 - iCalendar](https://tools.ietf.org/html/rfc5545)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [Libraires RRULE PHP](https://packagist.org/?query=rrule)

## Licence

Ce composant est fourni dans le cadre du système CMMS (Computerized Maintenance Management System).
