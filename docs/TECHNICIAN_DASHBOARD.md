# Dashboard Technicien - Système Informatif

## Vue d'ensemble
Le nouveau dashboard du technicien affiche un **calendrier informatif** des maintenances planifiées. Le technicien est maintenant **informé** des dates de maintenance au lieu de devoir les démarrer manuellement.

## Changement de Workflow

### ❌ Ancien Workflow
```
Technicien → Cherche une tâche
           → Démarre manuellement
           → Crée une WorkOrder
           → Lance l'intervention
```

### ✅ Nouveau Workflow  
```
Admin → Crée un Plan de Maintenance avec RRULE
      → Définit les dates de récurrence (quotidien, hebdo, etc.)
      
Système → Génère automatiquement les dates
        → Affiche dans le calendrier du technicien

Technicien → Voit le calendrier informatif
           → Sait exactement quand intervenir
           → Prépare son travail à l'avance
```

## Fonctionnalités du Dashboard

### 1. **Statistiques En-Tête**
```
📊 Plans Actifs          | 45 plans en cours
📅 Cette Semaine         | 12 interventions prévues  
📆 Prochaines 30 Jours   | 87 à planifier
```

### 2. **Widget Principal - Maintenance Planifiée**
Affiche **toutes les maintenances** pour les 30 prochains jours:
- 📅 **Date claire** (ex: "Mardi 18 février 2026")
- 🔴 **Indicateur de proximité** (Aujourd'hui, Demain, 3j., etc.)
- 🔧 **Équipement** avec code d'inventaire
- ⏰ **Heure** de l'intervention
- 🏷️ **Type** de maintenance (Préventive/Corrective)
- 📝 **Description** (si disponible)
- 📍 **Localisation** de l'équipement

### 3. **Widget Semaine Prochaine**
Affichage condensé des **prochains 7 jours**:
- Vue simplifiée pour vérification rapide
- Lien vers calendrier complet

## Utilisation

### Accès au Dashboard
1. Se connecter en tant que **Technicien**
2. Tableau de bord → Choisir l'option maintenances
3. URL: `/dashboard/maintenance`

### Consultation des Dates
1. **Voir toutes les dates** : Parcourrir le widget principal
2. **Plus de détails** : Cliquer "Voir le calendrier complet"
3. **Calendrier mois** : Aller à `/maintenance-calendar`

### Indicateurs Visuels

| Indicateur | Signification |
|-----------|--------------|
| 🟢 Point vert | Aujourd'hui |
| 🟠 Point orange | Demain |
| 🔵 Point bleu | Dans X jours |
| 🟤 Point gris | Date passée |

### Couleurs de Maintenance

| Couleur | Type |
|--------|------|
| 🟢 Vert | Maintenance Préventive |
| 🔴 Rouge | Maintenance Corrective |

## Integration avec RRULE

### Exemple de Récurrence
Admin crée un plan:
```
Équipement: Climatiseur Bureau
Fréquence: Hebdomadaire
Jours: Lundi, Mercredi, Vendredi
Heure: 09:00
Durée: 2 mois
```

**Résultat:** Le technicien voit automatiquement:
- Todas les lundis, mercredis, vendredis les 2 prochains mois
- À 09:00
- Pour le climatiseur du bureau

## Actualisation Automatique

Le dashboard se **rafraîchit automatiquement toutes les minutes** pour:
- Montrer les nouvelles maintenances ajoutées par l'admin
- Mettre à jour les indicateurs de proximité (aujourd'hui → hier, demain → aujourd'hui)
- Refléter les changements en temps réel

## Points Clés

✅ **Technicien informé en avance**
- Plus besoin de chercher les tâches
- Plus besoin de démarrer manuellement
- Préparation en amont

✅ **Admin contrôle total**
- Crée les récurrences (RRULE)
- Définit les équipements
- Fixe les horaires

✅ **Transparence**
- Tous les plans visibles
- Aucune tâche cachée
- Données à jour

## Dépannage

### "Aucune maintenance prévue"
- ✓ Admin a créé des plans actifs?
- ✓ Les plans ont des RRULE valides?
- ✓ Les dates sont dans les 30 jours?

### Les dates ne se mettent pas à jour
- Attendre le rafraîchissement automatique (max 60 secondes)
- Rafraîchir manuellement la page (F5)

### Une maintenance disparue
- Peut-être passée (date antérieure à aujourd'hui)
- Vérifier le calendrier complet

## URLs Utiles

| Page | URL |
|-----|-----|
| Dashboard Maintenance | `/dashboard/maintenance` |
| Calendrier Complet | `/maintenance-calendar` |
| Mes Interventions | `/technicien/workorders` |
| Profil Technicien | `/profile` |

---

**Dernière mise à jour:** 17 février 2026
**Version:** 1.0 - Système Informatif
