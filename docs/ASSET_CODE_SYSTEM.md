# Système de Code Unique d'Équipement (Asset Code)

## 📋 Vue d'ensemble

Ce système génère automatiquement des codes uniques pour identifier chaque équipement dans le CMMS, même lorsque plusieurs équipements portent le même nom.

---

## 🏗️ Structure du Code

### Format Standard
```
CAT-DEP-SITE-NNNN
```

### Format avec Année (optionnel)
```
CAT-DEP-SITE-YY-NNNN
```

### Composants

| Composant | Longueur | Description | Exemple |
|-----------|----------|-------------|---------|
| **CAT** | 3 lettres | Code de catégorie d'équipement | `ECR`, `IMP`, `MOT` |
| **DEP** | 3 lettres | Code du département | `ITC`, `PRD`, `MNT` |
| **SITE** | 3 lettres | Code du site/localisation | `USA`, `BPR`, `EN1` |
| **YY** | 2 chiffres | Année d'acquisition (optionnel) | `24`, `26` |
| **NNNN** | 4 chiffres | Numéro séquentiel | `0001`, `0042`, `1234` |

---

## 📚 Exemples Réels

### Informatique

| Équipement | Code | Signification |
|------------|------|---------------|
| Écran Dell 24" | `ECR-ITC-USA-0001` | 1er écran du département IT à l'Usine A |
| Écran Dell 24" | `ECR-ITC-USA-0002` | 2ème écran du département IT à l'Usine A |
| Imprimante HP | `IMP-ITC-BPR-0001` | 1ère imprimante IT au Bureau Principal |
| Laptop Lenovo | `LAP-ADM-BPR-0003` | 3ème laptop de l'Administration |
| Serveur Dell | `SRV-ITC-EN1-0001` | 1er serveur IT à l'Entrepôt 1 |

### Production

| Équipement | Code | Signification |
|------------|------|---------------|
| Moteur électrique 5kW | `MOT-PRD-USA-0001` | 1er moteur de Production à l'Usine A |
| Moteur électrique 5kW | `MOT-PRD-USA-0002` | 2ème moteur de Production à l'Usine A |
| Compresseur Atlas | `CPR-PRD-USB-0001` | 1er compresseur de Production à l'Usine B |
| Pompe centrifuge | `PMP-PRD-USA-0005` | 5ème pompe de Production |
| Convoyeur #3 | `CNV-PRD-USA-0003` | 3ème convoyeur |

### Maintenance

| Équipement | Code | Signification |
|------------|------|---------------|
| Tour CNC | `TOU-ATL-USA-0001` | 1er tour à l'Atelier |
| Fraiseuse | `FRA-ATL-USA-0002` | 2ème fraiseuse à l'Atelier |
| Poste à souder | `OUT-MNT-USB-0007` | 7ème outil de Maintenance |

### Avec Année d'Acquisition

| Équipement | Code | Signification |
|------------|------|---------------|
| Écran Samsung (2024) | `ECR-ITC-USA-24-0001` | 1er écran IT acheté en 2024 |
| Écran Samsung (2026) | `ECR-ITC-USA-26-0001` | 1er écran IT acheté en 2026 |
| Laptop Dell (2025) | `LAP-ADM-BPR-25-0015` | 15ème laptop Admin acheté en 2025 |

---

## 🗄️ Structure de la Base de Données

### Champs Ajoutés à la Table `equipements`

```php
Schema::table('equipements', function (Blueprint $table) {
    // Champs pour la génération du code
    $table->string('type_equipement', 50)->nullable();  // Informatique, Électrique, Mécanique
    $table->string('categorie', 50)->nullable();         // Écran, Imprimante, Moteur, Pompe
    $table->string('departement', 50)->nullable();       // Production, IT, Maintenance
    $table->string('site', 50)->nullable();              // Usine A, Bureau B, Entrepôt 1
    $table->year('annee_acquisition')->nullable();       // 2024, 2025, 2026
});
```

### Champs Existants Utilisés

- `code` : Code unique généré (ex: `ECR-ITC-USA-0001`)
- `nom` : Nom de l'équipement (ex: "Écran Dell 24 pouces")
- `numero_serie` : Numéro de série constructeur
- `localisation` : Emplacement précis (ex: "Salle 201, Bureau 3")

---

## ⚙️ Utilisation

### 1. Migration

```bash
php artisan migrate
```

### 2. Création d'Équipement (Code Auto-Généré)

```php
use App\Models\Equipement;

$equipement = Equipement::create([
    'nom' => 'Écran Dell 24 pouces',
    'categorie' => 'Écran',
    'departement' => 'IT',
    'site' => 'Usine A',
    'marque' => 'Dell',
    'modele' => 'P2422H',
    'numero_serie' => 'SN123456789',
    'annee_acquisition' => 2024,
    'statut' => 'actif',
    'localisation' => 'Bureau 201',
]);

// Le code est automatiquement généré: ECR-ITC-USA-0001
echo $equipement->code; // ECR-ITC-USA-0001
echo $equipement->full_name; // ECR-ITC-USA-0001 - Écran Dell 24 pouces
```

### 3. Génération Manuelle (Optionnel)

```php
use App\Services\AssetCodeGenerator;

$generator = new AssetCodeGenerator();

// Format standard
$code = $generator->generate($equipement);
// Résultat: ECR-ITC-USA-0001

// Format avec année
$code = $generator->generateWithYear($equipement, true);
// Résultat: ECR-ITC-USA-24-0001
```

### 4. Validation d'un Code

```php
$generator = new AssetCodeGenerator();

$isValid = $generator->validate('ECR-ITC-USA-0001'); // true
$isValid = $generator->validate('INVALID-CODE');     // false
```

### 5. Recherche par Département/Site

```php
// Tous les équipements du département IT
$equipementsIT = Equipement::byDepartement('IT')->get();

// Tous les équipements de l'Usine A
$equipementsUsineA = Equipement::bySite('Usine A')->get();

// Tous les écrans
$ecrans = Equipement::byCategorie('Écran')->get();
```

---

## 🎯 Codes Prédéfinis

### Catégories d'Équipements

#### Informatique
- `ECR` - Écran
- `ORD` - Ordinateur
- `IMP` - Imprimante
- `SRV` - Serveur
- `SWT` - Switch
- `ROU` - Routeur
- `SCN` - Scanner
- `LAP` - Laptop

#### Électrique
- `TRF` - Transformateur
- `GEN` - Générateur
- `OND` - Onduleur
- `TAB` - Tableau électrique
- `MOT` - Moteur électrique

#### Mécanique
- `PMP` - Pompe
- `CPR` - Compresseur
- `VEN` - Ventilateur
- `CNV` - Convoyeur
- `PRS` - Presse
- `TOU` - Tour
- `FRA` - Fraiseuse

#### Climatisation/Chauffage
- `CLM` - Climatiseur
- `CHD` - Chaudière
- `RAD` - Radiateur

#### Autres
- `VEH` - Véhicule
- `MOB` - Mobilier
- `OUT` - Outil

### Départements

- `PRD` - Production
- `MNT` - Maintenance
- `ITC` - IT / Informatique
- `LOG` - Logistique
- `QLT` - Qualité
- `ADM` - Administration
- `RHU` - Ressources Humaines
- `SEC` - Sécurité
- `MAG` - Magasin
- `ATL` - Atelier
- `BUR` - Bureau
- `ENT` - Entrepôt

### Sites

- `USA` - Usine A
- `USB` - Usine B
- `BPR` - Bureau Principal
- `EN1` - Entrepôt 1
- `EN2` - Entrepôt 2
- `SNO` - Site Nord
- `SUD` - Site Sud
- `EST` - Site Est
- `OUE` - Site Ouest

---

## 🔧 Personnalisation

### Ajouter de Nouvelles Catégories

Dans `app/Services/AssetCodeGenerator.php`, ajoutez vos codes:

```php
private const CATEGORY_CODES = [
    // Vos catégories
    'Robot' => 'ROB',
    'Drone' => 'DRN',
    'Caméra' => 'CAM',
    // ...
];
```

### Ajouter de Nouveaux Départements

```php
private const DEPARTMENT_CODES = [
    // Vos départements
    'Marketing' => 'MKT',
    'Ventes' => 'VNT',
    'Recherche' => 'REC',
    // ...
];
```

### Ajouter de Nouveaux Sites

```php
private const SITE_CODES = [
    // Vos sites
    'Usine C' => 'USC',
    'Filiale Paris' => 'PAR',
    'Centre Formation' => 'CFR',
    // ...
];
```

---

## 📊 Avantages du Système

### ✅ Lisibilité
- **Immédiatement compréhensible** : `ECR-ITC-USA-0001` = Écran, IT, Usine A, 1er équipement
- **Structure logique** : catégorie → département → site → numéro

### ✅ Unicité Garantie
- **Combinaison unique** : même nom, même marque, mais codes différents
- **Séquence automatique** : incrémentation par contexte (catégorie + département + site)

### ✅ Traçabilité
- **Identification rapide** de la localisation et du type
- **Historique** : option d'inclure l'année d'acquisition
- **Groupement facile** : tous les codes commençant par `ECR-ITC-` = écrans du département IT

### ✅ Scalabilité
- **9,999 équipements** par combinaison catégorie/département/site
- **Extensible** : ajout facile de nouveaux codes
- **Flexible** : génération automatique pour codes non prédéfinis

---

## 🚀 Exemple de Workflow Complet

```php
// 1. Créer un équipement
$ecran = Equipement::create([
    'nom' => 'Écran Samsung 27"',
    'categorie' => 'Écran',           // Génère: ECR
    'departement' => 'IT',            // Génère: ITC
    'site' => 'Usine A',              // Génère: USA
    'marque' => 'Samsung',
    'modele' => 'S27A600',
    'numero_serie' => 'SAM987654321',
    'annee_acquisition' => 2026,
    'statut' => 'actif',
    'responsable_id' => 5,
]);

// Code automatiquement généré: ECR-ITC-USA-0001
echo $ecran->code;

// 2. Créer un deuxième écran identique
$ecran2 = Equipement::create([
    'nom' => 'Écran Samsung 27"',     // Même nom !
    'categorie' => 'Écran',
    'departement' => 'IT',
    'site' => 'Usine A',
    'marque' => 'Samsung',
    'modele' => 'S27A600',            // Même modèle !
    'numero_serie' => 'SAM111222333', // Numéro de série différent
    'annee_acquisition' => 2026,
    'statut' => 'actif',
]);

// Code différent: ECR-ITC-USA-0002
echo $ecran2->code;

// 3. Recherche et affichage
$ecrans = Equipement::byDepartement('IT')
    ->byCategorie('Écran')
    ->get();

foreach ($ecrans as $e) {
    echo "{$e->full_name} ({$e->numero_serie})\n";
}
// ECR-ITC-USA-0001 - Écran Samsung 27" (SAM987654321)
// ECR-ITC-USA-0002 - Écran Samsung 27" (SAM111222333)
```

---

## 📝 Notes Importantes

1. **Champ `code` unique** : contrainte de base de données garantit l'unicité
2. **Génération automatique** : lors de la création si le code n'est pas fourni
3. **Code manuel possible** : vous pouvez toujours fournir un code personnalisé
4. **Validation** : utilisez `AssetCodeGenerator::validate()` pour vérifier le format
5. **Extensibilité** : ajoutez facilement de nouveaux codes sans modifier la logique

---

## 🆘 Support

Pour ajouter de nouveaux codes ou personnaliser le système :
- Modifier `app/Services/AssetCodeGenerator.php`
- Les codes sont générés automatiquement pour les catégories non prédéfinies
- Le format reste toujours cohérent et lisible

---

**Date de création** : 2 février 2026  
**Version** : 1.0  
**Auteur** : Système CMMS Laravel
