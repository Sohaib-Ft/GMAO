# 🚀 Démarrage Rapide - Système de Code Unique

## Installation

### 1. Exécuter la migration

```bash
php artisan migrate
```

### 2. (Optionnel) Générer des exemples

```bash
php artisan db:seed --class=EquipementSeeder
```

---

## Utilisation Simple

### Créer un équipement (code auto-généré)

```php
use App\Models\Equipement;

$equipement = Equipement::create([
    'nom' => 'Écran Dell 24"',
    'categorie' => 'Écran',
    'departement' => 'IT',
    'site' => 'Usine A',
    'marque' => 'Dell',
    'numero_serie' => 'DELL123',
    'statut' => 'actif',
]);

// Le code est automatiquement généré
echo $equipement->code; // ECR-ITC-USA-0001
```

---

## Structure du Code

```
ECR-ITC-USA-0001
│   │   │   │
│   │   │   └─── Numéro séquentiel (4 chiffres)
│   │   └─────── Site (3 lettres)
│   └─────────── Département (3 lettres)
└─────────────── Catégorie (3 lettres)
```

---

## Exemples Rapides

| Équipement | Code Généré |
|------------|-------------|
| Écran Dell (IT, Usine A) | `ECR-ITC-USA-0001` |
| Écran Dell (IT, Usine A) | `ECR-ITC-USA-0002` |
| Laptop (Admin, Bureau) | `LAP-ADM-BPR-0001` |
| Moteur 5kW (Prod, Usine A) | `MOT-PRD-USA-0001` |
| Compresseur (Prod, Usine B) | `CPR-PRD-USB-0001` |

---

## Personnalisation

Éditez `config/asset.php` pour ajouter vos propres codes :

```php
'categories' => [
    'Votre Catégorie' => 'VOT',
],

'departments' => [
    'Votre Département' => 'VDP',
],

'sites' => [
    'Votre Site' => 'VSI',
],
```

---

## Documentation Complète

Consultez [docs/ASSET_CODE_SYSTEM.md](../docs/ASSET_CODE_SYSTEM.md) pour :
- Liste complète des codes prédéfinis
- Exemples détaillés
- API et méthodes avancées
- Guide de personnalisation

---

## Commandes Utiles

```bash
# Lancer la migration
php artisan migrate

# Générer des équipements d'exemple
php artisan db:seed --class=EquipementSeeder

# Rafraîchir la base de données
php artisan migrate:fresh --seed
```

---

## Support

Pour toute question sur le système de code :
- Consultez `docs/ASSET_CODE_SYSTEM.md`
- Éditez `app/Services/AssetCodeGenerator.php`
- Modifiez `config/asset.php`
