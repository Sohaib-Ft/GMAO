# ⚡ Guide de Démarrage Rapide - RRULE Generator

Vous avez 5 minutes? Mettez en marche le générateur RRULE !

## 🎯 En 3 Étapes

### 1️⃣ Utiliser le Composant (1 minute)

Dans votre Blade view:

```blade
<form method="POST" action="/maintenance-plans">
    @csrf
    
    <!-- Le composant RRULE -->
    <x-rrule-generator 
        name="rrule" 
        :value="old('rrule')"
        label="Récurrence"
        :required="true"
    />
    
    <button type="submit">Créer</button>
</form>
```

C'est tout! Le composant gère tout.

### 2️⃣ Valider en Backend (1 minute)

```php
// app/Http/Requests/StoreMaintenancePlanRequest.php
use App\Services\RruleParser;

public function rules(): array
{
    return [
        'rrule' => [
            'required',
            function ($attribute, $value, $fail) {
                if (!RruleParser::isValidRrule($value)) {
                    $fail('RRULE invalide');
                }
            }
        ]
    ];
}
```

### 3️⃣ Utiliser dans votre Modèle (1 minute)

```php
// app/Models/MaintenancePlan.php
$plan = MaintenancePlan::find(1);

// Accéder à la RRULE brute
echo $plan->rrule;
// Sortie: FREQ=WEEKLY;BYDAY=MO,WE,FR

// Résumé en français
$parser = new RruleParser($plan->rrule);
echo $parser->toFrench();
// Sortie: Se répète le lundi, mercredi et vendredi

// Prochaines occurrences
$dates = $parser->getNextOccurrences(now(), 10);
```

## 📋 Checklist

- [x] Composant créé: `resources/views/components/rrule-generator.blade.php`
- [x] Service créé: `app/Services/RruleParser.php`
- [x] Configuration créée: `config/rrule.php`
- [x] Validation intégrée: `app/Http/Requests/StoreMaintenancePlanRequest.php`
- [x] Vue mise à jour: `resources/views/admin/maintenance_plans/create.blade.php`
- [ ] **À vous de jouer!**

## 💡 Exemples Courants

### Maintenance Hebdomadaire
```blade
<!-- L'utilisateur sélectionne: Hebdomadaire → Lundi, Mercredi, Vendredi -->
<!-- Sortie: -->
{{ $plan->rrule }} <!-- FREQ=WEEKLY;BYDAY=MO,WE,FR -->
```

### Service Mensuel
```blade
<!-- L'utilisateur sélectionne: Mensuel → Le 15 -->
{{ $plan->rrule }} <!-- FREQ=MONTHLY;BYMONTHDAY=15 -->
```

### Inspection Trimestrielle
```blade
<!-- L'utilisateur sélectionne: Mensuel + Intervalle 3 + 1er lundi -->
{{ $plan->rrule }} <!-- FREQ=MONTHLY;INTERVAL=3;BYDAY=1MO -->
```

## 🧪 Tester Rapidement

```bash
# Ouvrir Tinker
php artisan tinker

# Validater une RRULE
>>> App\Services\RruleParser::isValidRrule('FREQ=WEEKLY;BYDAY=MO,WE,FR')
true

# Parser une RRULE
>>> $parser = new App\Services\RruleParser('FREQ=WEEKLY;BYDAY=MO,WE,FR')
>>> $parser->toFrench()
"Se répète le lundi, mercredi et vendredi"

# Exporter
>>> dd($parser->getParts())
```

## 🎨 Personnaliser les Couleurs

Dans `resources/views/components/rrule-generator.blade.php`:

```blade
<!-- Chercher -->
:class="frequency === 'DAILY' 
    ? 'bg-indigo-600 text-white'  ← Changez ces couleurs!
    : 'bg-gray-50 text-gray-700'"

<!-- Remplacer indigo par votre couleur: blue, green, red, etc. -->
```

## 📱 Responsive?

Oui! Le composant est mobile-first et utilise Tailwind grid:
- Mobile: 1 colonne
- Tablette: 2 colonnes
- Desktop: 4 colonnes (pour les jours)

## ❓ Questions Fréquentes

**Q: Le composant n'apparaît pas**
A: Vérifier que Alpine.js est chargé dans votre layout

**Q: Comment afficher les prochaines dates?**
```php
$parser = new RruleParser($plan->rrule);
$nextDates = $parser->getNextOccurrences(now(), 12);
```

**Q: Comment persister la RRULE?**
```php
// Elle est automatiquement sauvegardée dans la DB
MaintenancePlan::create(['rrule' => 'FREQ=WEEKLY;BYDAY=MO,WE,FR']);
```

**Q: Quels formats d'intervalle?**
- Quotidien: jours
- Hebdomadaire: semaines
- Mensuel: mois
- Annuel: années

## 🔗 Ressources

| Document | Usage |
|----------|-------|
| [RRULE_GENERATOR.md](docs/RRULE_GENERATOR.md) | Documentation complète |
| [RRULE_INTEGRATION_GUIDE.md](docs/RRULE_INTEGRATION_GUIDE.md) | Guide d'intégration |
| [RFC 5545](https://tools.ietf.org/html/rfc5545) | Spécification |

## 🎉 Prêt!

Vous avez maintenant un générateur RRULE:
- ✅ Facile à utiliser
- ✅ Entièrement validé
- ✅ Prêt pour la production
- ✅ Bien documenté

Rendez-vous à [demo.blade.php](resources/views/admin/maintenance_plans/demo.blade.php) pour voir une démonstration interactive!

---

**Besoin de plus?** Consultez la documentation complète!
