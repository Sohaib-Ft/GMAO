# 📅 Calendrier de Maintenance pour les Techniciens

## Vue d'ensemble

Le calendrier de maintenance affiche visuellement les interventions planifiées basées sur les règles RRULE des plans de maintenance. Les techniciens peuvent voir:

- 📋 Calendrier mensuel avec les dates d'intervention
- 📊 Calendrier trimestriel pour la planification à long terme
- 📈 Calendrier annuel pour une vue globale
- 📅 Vue hebdomadaire détaillée
- 📱 Statistiques d'intervention

## 🎯 Fonctionnalités

### 1. Calendrier Mensuel
```
[L][M][M][J][V][S][D]
                     1 • (maintenance)
2  3 • 4       5 •    6  7
...
```

Affiche:
- Les jours du mois
- Les points indiquant une maintenance
- Surbrillance du mois actuel vs jours précédents/suivants
- Tooltip au survol pour les détails

### 2. Navigation
- Boutons "Précédent" / "Suivant"
- Bouton "Aujourd'hui" pour revenir au mois courant
- Changement de vue (Mois, Trimestre, Année)

### 3. Filtrage
- Par équipement
- Par technicien
- Par type (préventive/corrective)
- Par vue (month/quarter/year)

### 4. Vue Hebdomadaire Détaillée
```
│ Lun 17 │ Mar 18 │ Mer 19 │ Jeu 20 │ Ven 21 │ Sam 22 │ Dim 23 │
│        │   •    │        │   •    │    •   │        │        │
│        │Equipment1
           09:00
```

### 5. Prochaines Interventions
- Lister les 20 prochaines maintenances (30 jours)
- Affichage avec équipement, date et technicien assigné
- Classées par date

### 6. Statistiques
- Total de plans actifs
- Nombre d'interventions ce mois
- Nombre d'interventions dans les 30 jours
- Répartition par technicien

## 📊 Données Affichées

### Par Date
```json
{
  "date": "2026-02-17",
  "has_occurrence": true,
  "occurrences": [
    {
      "plan": {...},
      "equipement": "Compresseur-001",
      "date": "2026-02-17T09:00:00"
    }
  ]
}
```

### Par Intervention
```json
{
  "plan": MaintenancePlan,
  "equipement": Equipement,
  "date": "2026-02-17T09:00:00"
}
```

## 🔧 Utilisation du Calendrier

### Depuis le Dashboard Technicien
```blade
<!-- Accéder au calendrier -->
<a href="{{ route('maintenance-calendar') }}">
    📅 Voir le calendrier
</a>
```

### Avec Filtres
```blade
<!-- Calendrier filtré pour un équipement -->
<a href="{{ route('maintenance-calendar', ['equipement' => 1]) }}">
    Calendrier - Équipement
</a>

<!-- Calendrier filtré pour un technicien -->
<a href="{{ route('maintenance-calendar', ['technicien' => 3]) }}">
    Mes maintenances
</a>
```

### Changers de Vue
```
Vue par défaut: Mois
- Trimestre: Affiche 3 mois consécutifs
- Année: Affiche 12 mois
```

## 🎨 Design

### Calendrier Mensuel
- **Header**: Gradient bleu/indigo avec mois et résumé RRULE
- **Grille**: 7 colonnes (L-D), 6 rangées max
- **Jours**: 
  - Blanc si dans le mois actuel
  - Gris si hors du mois
  - Point indigo = maintenance
- **Hover**: Tooltip avec heure et détails
- **Legend**: Affiche le nombre d'occurrences

### Calendrier Hebdomadaire
- **7 colonnes**: Un jour par colonne
- **Jour actuel**: Bordure indigo en gras
- **Intervalle horaire**: Affichage libre (pas de grille horaire)
- **Cartes maintenance**: Fond indigo clair, cliquable

### Statistiques
- **Cards métriques**: Gradient de couleurs (indigo, vert, orange)
- **Répartition technicien**: Petit tableau avec décompte
- **Number large**: Font-size XXL en gras

## 📱 Responsive

- **Mobile** (<640px):
  - Calendrier mensuel: Cellules plus petites, texte réduit
  - Vue hebdomadaire: 2-3 jours par ligne
  - Filtres: Stacked verticalement
  
- **Tablette** (640px-1024px):
  - Calendrier complet visible
  - 2 colonnes pour stats
  
- **Desktop** (>1024px):
  - Calendrier + Stats côte à côte
  - 3 colonnes pour stats

## 🔐 Sécurité

- ✅ Visible seulement pour techniciens et admins
- ✅ Chaque utilisateur voit ses propres maintenances
- ✅ Filtrage par technicien assigné disponible
- ✅ Logs des accès au calendrier

## ⚡ Performance

- **Calcul RRULE**: Limité à 100 occurrences par plan
- **Filtre mensuel**: Occurrences dans la plage du mois
- **Cache possible**: Les calendriers peuvent être cachés 1h
- **Lazy loading**: Les détails au survol

## 🔄 Mise à Jour

Le calendrier se met à jour automatiquement quand:
- Un nouveau plan de maintenance est créé
- Un plan est modifié (RRULE changée)
- Un plan est désactivé
- Une intervention est complétée

## 📋 Cas d'Usage

### Cas 1: Technicien voit ses maintenances
```
1. Accéder à http://localhost/maintenance-calendar
2. Filtre automatique sur ses maintenances assignées
3. Voir le calendrier de ses interventions
```

### Cas 2: Admin planifie les équipes
```
1. Accéder au calendrier
2. Filtrer par équipement critique
3. Valider la charge de travail
4. Réassigner si nécessaire
```

### Cas 3: Technicien planifie sa semaine
```
1. Cliquer sur la vue "Semaine"
2. Voir les 7 jours avec détails
3. Cliquer sur une intervention pour plus d'infos
4. Marquer comme complétée si terminée
```

## 📊 Statistiques Disponibles

```
✅ Total de plans actifs
✅ Nombre d'interventions ce mois
✅ Nombre d'interventions 30 jours
✅ Répartition par technicien
✅ Type de maintenance (préventive/corrective)
```

## 🔗 Intégration RRULE

Le calendrier utilise la classe `RruleParser` pour:

1. **Parser la RRULE** d'un plan
2. **Générer les occurrences** du mois
3. **Créer un résumé français** affiché en haut du calendrier
4. **Calculer les dates** des interventions futures

Exemple:
```php
$parser = new RruleParser('FREQ=WEEKLY;BYDAY=MO,WE,FR');
$occurrences = $parser->getNextOccurrences(now(), 100);
// Retourne les dates des prochaines interventions
```

## 📚 Documentation Complète

- [RRULE_GENERATOR.md](RRULE_GENERATOR.md) - Système RRULE
- [CalendarGenerator](../app/Services/CalendarGenerator.php) - Service du calendrier
- [MaintenanceCalendarController](../app/Http/Controllers/MaintenanceCalendarController.php) - Logique

## 🎓 Exemples

### Vue Mensuelle
```bash
GET /maintenance-calendar
GET /maintenance-calendar?view=month&month=2&year=2026
```

### Vue Trimestrielle
```bash
GET /maintenance-calendar?view=quarter
```

### Vue Annuelle
```bash
GET /maintenance-calendar?view=year
```

### Filtrée par Équipement
```bash
GET /maintenance-calendar?equipement=5
```

### Filtrée par Technicien
```bash
GET /maintenance-calendar?technicien=3
```

## ⚙️ Configuration

Aucune configuration requise. Le calendrier utilise:
- La configuration `config/rrule.php` pour les formats
- Le service `CalendarGenerator` pour la logique
- Le contrôleur `MaintenanceCalendarController` pour le routing

## 🆘 Troubleshooting

**Q: Les dates ne s'affichent pas**
- Vérifier que les plans ont une RRULE valide
- Vérifier que `statut` = 'actif'
- Consulter les logs pour les erreurs de parsing

**Q: Le calendrier est vide**
- Créer un plan de maintenance avec une RRULE
- Vérifier que la date d'aujourd'hui est après la création du plan
- Vérifier le filtre par équipement/technicien

**Q: Les statistiques sont fausses**
- Rafraîchir la page
- Vérifier le filtre sélectionné
- Vérifier la configuration RRULE

---

**Version**: 1.0.0  
**Status**: ✅ Production Ready  
**Last Updated**: 2026-02-17
