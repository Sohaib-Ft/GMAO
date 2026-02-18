# 📅 Générateur de Récurrence RRULE - Vue d'ensemble

Un composant moderne et intuitif pour générer des règles de récurrence (RRULE) compatibles avec la RFC 5545, sans connaître la syntaxe technique.

## 🎯 Fonctionnalités

✅ **Sélecteur de Fréquence** - Quotidien, Hebdomadaire, Mensuel, Annuel  
✅ **Configuration Dynamique** - Adaptée à chaque fréquence  
✅ **Résumé en Français** - Traduit automatiquement en langage naturel  
✅ **RRULE Automatique** - Génère la chaîne RFC 5545 en temps réel  
✅ **Design Enterprise** - Minimaliste avec Tailwind CSS  
✅ **Réactivité Alpine.js** - Interactions fluides et rapides  

## 📁 Fichiers Créés

```
.
├── resources/
│   └── views/
│       └── components/
│           └── rrule-generator.blade.php      ← Composant principal
│
├── app/
│   ├── Services/
│   │   └── RruleParser.php                    ← Service de parsing
│   └── Http/
│       ├── Requests/
│       │   └── StoreMaintenancePlanRequest.php  ← Validation avec RRULE
│       └── Controllers/
│           └── MaintenancePlanControllerExample.php
│
├── config/
│   └── rrule.php                              ← Configuration
│
├── tests/
│   └── Unit/
│       └── RruleParserTest.php                ← Tests unitaires
│
└── docs/
    ├── RRULE_GENERATOR.md                     ← Documentation complète
    ├── RRULE_INTEGRATION_GUIDE.md             ← Guide d'intégration
    └── README.md                              ← Ce fichier
```

## 🚀 Démarrage Rapide

### 1. Utiliser le Composant dans une Vue

```blade
<!-- resources/views/admin/maintenance_plans/create.blade.php -->
<x-rrule-generator 
    name="rrule" 
    :value="old('rrule')"
    label="Règle de Récurrence"
    :required="true"
/>
```

### 2. Valider en Backend

```php
// app/Http/Requests/StoreMaintenancePlanRequest.php
use App\Services\RruleParser;

public function rules(): array
{
    return [
        'rrule' => [
            'required',
            'string',
            function ($attribute, $value, $fail) {
                if (!RruleParser::isValidRrule($value)) {
                    $fail('RRULE invalide');
                }
            }
        ]
    ];
}
```

### 3. Utiliser dans le Modèle

```php
// app/Models/MaintenancePlan.php
$plan = MaintenancePlan::find(1);
echo $plan->rrule_details['french_summary'];  
// Sortie: "Se répète le lundi, mercredi et vendredi"
```

## 💡 Exemples d'Utilisation

### Quotidien
```
Interface: Quotidien → "Tous les 1 jour"
Output: FREQ=DAILY
```

### Hebdomadaire
```
Interface: Hebdo → L, M, M, J, V → "Se répète le lundi, mardi, mercredi, jeudi et vendredi"
Output: FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR
```

### Mensuel (jour fixe)
```
Interface: Mensuel → 15 → "Se répète le 15 de chaque mois"
Output: FREQ=MONTHLY;BYMONTHDAY=15
```

### Mensuel (jour relatif)
```
Interface: Mensuel → Deuxième lundi → "Se répète le deuxième lundi de chaque mois"
Output: FREQ=MONTHLY;BYDAY=2MO
```

### Annuel
```
Interface: Annuel → "Se répète chaque année"
Output: FREQ=YEARLY
```

### Avec Intervalle
```
Interface: Tous les 3 mois → "Se répète tous les 3 mois"
Output: FREQ=MONTHLY;INTERVAL=3;BYMONTHDAY=1
```

## 🧪 Tests

Exécuter les tests unitaires:

```bash
php artisan test tests/Unit/RruleParserTest.php
```

Tests disponibles:
- ✅ Validation RRULE
- ✅ Parsing des paramètres
- ✅ Résumés en français
- ✅ Génération de RRULE
- ✅ Gestion des occurrences

## 📊 Architecture

```
┌─────────────────────────────────────────┐
│  Blade Component                       │
│  rrule-generator.blade.php            │
└──────────────────┬──────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
    Alpine.js            Tailwind CSS
   React Logic           Styling
        │                     │
        └──────────┬──────────┘
                   │
        ┌──────────▼──────────┐
        │   Hidden Input      │
        │  name="rrule"       │
        └─────────────────────┘
                   │
                   │ Form Submit
                   ▼
        ┌──────────────────────┐
        │  StoreRequest        │
        │  Validation          │
        └─────────────────────┘
                   │
                   │ Validated
                   ▼
      ┌────────────────────────┐
      │  RruleParser Service   │
      │  - Parse RRULE         │
      │  - Validate            │
      │  - toFrench()          │
      │  - getOccurrences()    │
      └────────────────────────┘
                   │
                   │ Processed
                   ▼
        ┌──────────────────────┐
        │  MaintenancePlan     │
        │  Model - Save RRULE  │
        └──────────────────────┘
```

## 🔧 Configuration

Modifier `config/rrule.php` pour personnaliser:

```php
'frequencies' => [
    'DAILY' => ['label' => 'Quotidien', ...],
    'WEEKLY' => ['label' => 'Hebdomadaire', ...],
    'MONTHLY' => ['label' => 'Mensuel', ...],
    'YEARLY' => ['label' => 'Annuel', ...],
],

'weekdays' => [
    'MO' => ['short' => 'L', 'long' => 'Lundi'],
    // ...
],

'default_weekdays' => ['MO', 'WE', 'FR'],
```

## 🎨 Personnalisation

### Changer les couleurs

Modifier le composant Blade:
```blade
:class="frequency === 'DAILY' 
    ? 'bg-indigo-600 text-white'  ← Changer ces couleurs
    : 'bg-gray-50 text-gray-700'"
```

### Ajouter des présets

Dans `config/rrule.php`:
```php
'presets' => [
    [
        'label' => 'Maintenance Hebdo Courante',
        'rrule' => 'FREQ=WEEKLY;BYDAY=MO,WE,FR',
        'description' => 'Configuration recommandée',
    ],
    // ...
]
```

### Traductions multilingues

```blade
<!-- Remplacer les textes par des traductions -->
<span x-text="frequency === 'DAILY' 
    ? '{{ __('Quotidien') }}' 
    : ''"></span>
```

## 📚 Documentation

- **[RRULE_GENERATOR.md](docs/RRULE_GENERATOR.md)** - Documentation complète et détaillée
- **[RRULE_INTEGRATION_GUIDE.md](docs/RRULE_INTEGRATION_GUIDE.md)** - Guide d'intégration pas à pas
- **[RFC 5545](https://tools.ietf.org/html/rfc5545)** - Spécification iCalendar

## ⚙️ Prérequis

- Laravel 10+
- PHP 8.1+
- Alpine.js 3.x
- Tailwind CSS 3.x
- Blade Components support

## 🔒 Sécurité

- ✅ Validation stricte de la RRULE (regex RFC 5545)
- ✅ Protection CSRF sur les formulaires
- ✅ Sanitisation des entrées
- ✅ Authorization checks dans le contrôleur

## 📈 Performance

- Pas de dépendances heavy
- Calculs côté client (Alpine.js)
- Validation côté serveur uniquement
- Cache-friendly pour les occurrences

## 🐛 Troubleshooting

### Le composant n'apparaît pas
1. Vérifier Alpine.js est chargé: `console.log(window.Alpine)`
2. Vérifier la syntaxe: `<x-rrule-generator />`
3. Vérifier le dossier: `resources/views/components/`

### RRULE non généré
1. Vérifier le formulaire contient `<input type="hidden" name="rrule">`
2. Ouvrir la DevTools (F12) et vérifier la console
3. Vérifier Alpine.js n'a pas d'erreurs

### Validation échoue
1. Vérifier la RRULE commence par `FREQ=`
2. Tester avec: `RruleParser::isValidRrule('...')`
3. Consulter les logs: `storage/logs/laravel.log`

## 🤝 Contribution

Pour améliorer le composant:
1. Consulter la documentation
2. Ajouter des tests
3. Vérifier les validations
4. Créer un PR

## 📝 Licence

Développé pour le système CMMS (Computerized Maintenance Management System).

## 🎓 En Savoir Plus

```bash
# Lire les documentations complètes
cat docs/RRULE_GENERATOR.md
cat docs/RRULE_INTEGRATION_GUIDE.md

# Exécuter les tests
php artisan test

# Vérifier la validation RRULE
php artisan tinker
> App\Services\RruleParser::isValidRrule('FREQ=WEEKLY;BYDAY=MO,WE,FR')
```

## 📞 Support

Besoin d'aide?
1. Consulter la documentation
2. Vérifier les tests
3. Examiner les logs Laravel
4. Vérifier la console du navigateur

---

**Version:** 1.0.0  
**Dernière mise à jour:** 2026-02-17  
**Status:** ✅ Production Ready
