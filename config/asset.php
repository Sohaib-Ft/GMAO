<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Format du Code d'Équipement
    |--------------------------------------------------------------------------
    |
    | Choisissez le format de code par défaut pour vos équipements
    | 'standard' : CAT-DEP-SITE-NNNN
    | 'with_year' : CAT-DEP-SITE-YY-NNNN
    |
    */

    'code_format' => env('ASSET_CODE_FORMAT', 'standard'),

    /*
    |--------------------------------------------------------------------------
    | Longueur du Numéro de Séquence
    |--------------------------------------------------------------------------
    |
    | Définit le nombre de chiffres pour le numéro séquentiel
    | Par défaut: 4 (0001 à 9999)
    |
    */

    'sequence_length' => 4,

    /*
    |--------------------------------------------------------------------------
    | Catégories d'Équipements
    |--------------------------------------------------------------------------
    |
    | Définissez vos propres catégories et leurs codes (3 lettres)
    |
    */

    'categories' => [
        // Informatique
        'Écran' => 'ECR',
        'Ordinateur' => 'ORD',
        'Imprimante' => 'IMP',
        'Serveur' => 'SRV',
        'Switch' => 'SWT',
        'Routeur' => 'ROU',
        'Scanner' => 'SCN',
        'Laptop' => 'LAP',
        'Tablette' => 'TAB',
        'Smartphone' => 'SPH',
        
        // Électrique
        'Transformateur' => 'TRF',
        'Générateur' => 'GEN',
        'Onduleur' => 'OND',
        'Tableau électrique' => 'TBL',
        'Moteur électrique' => 'MOT',
        'Disjoncteur' => 'DIS',
        
        // Mécanique
        'Pompe' => 'PMP',
        'Compresseur' => 'CPR',
        'Ventilateur' => 'VEN',
        'Convoyeur' => 'CNV',
        'Presse' => 'PRS',
        'Tour' => 'TOU',
        'Fraiseuse' => 'FRA',
        'Perceuse' => 'PRC',
        'Robot' => 'ROB',
        
        // Climatisation/Chauffage
        'Climatiseur' => 'CLM',
        'Chaudière' => 'CHD',
        'Radiateur' => 'RAD',
        'CTA' => 'CTA', // Centrale de Traitement d'Air
        
        // Transport
        'Véhicule' => 'VEH',
        'Chariot élévateur' => 'CHE',
        'Transpalette' => 'TRP',
        
        // Autres
        'Mobilier' => 'MOB',
        'Outil' => 'OUT',
        'Instrument de mesure' => 'INS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Départements
    |--------------------------------------------------------------------------
    |
    | Définissez vos départements et leurs codes (3 lettres)
    |
    */

    'departments' => [
        'Production' => 'PRD',
        'Maintenance' => 'MNT',
        'IT' => 'ITC',
        'Informatique' => 'ITC',
        'Logistique' => 'LOG',
        'Qualité' => 'QLT',
        'Administration' => 'ADM',
        'RH' => 'RHU',
        'Ressources Humaines' => 'RHU',
        'Sécurité' => 'SEC',
        'Magasin' => 'MAG',
        'Atelier' => 'ATL',
        'Bureau' => 'BUR',
        'Entrepôt' => 'ENT',
        'Commercial' => 'COM',
        'Marketing' => 'MKT',
        'Achats' => 'ACH',
        'Finance' => 'FIN',
        'Direction' => 'DIR',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sites / Localisations
    |--------------------------------------------------------------------------
    |
    | Définissez vos sites et leurs codes (3 lettres)
    |
    */

    'sites' => [
        'Usine A' => 'USA',
        'Usine B' => 'USB',
        'Usine C' => 'USC',
        'Bureau Principal' => 'BPR',
        'Entrepôt 1' => 'EN1',
        'Entrepôt 2' => 'EN2',
        'Entrepôt 3' => 'EN3',
        'Site Nord' => 'SNO',
        'Site Sud' => 'SUD',
        'Site Est' => 'EST',
        'Site Ouest' => 'OUE',
        'Siège Social' => 'SIE',
        'Filiale' => 'FIL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Code par Défaut
    |--------------------------------------------------------------------------
    |
    | Codes utilisés quand la catégorie, le département ou le site n'est pas défini
    |
    */

    'default_codes' => [
        'category' => 'GEN',    // Generic
        'department' => 'GEN',  // Generic
        'site' => 'DEF',        // Default
    ],

    /*
    |--------------------------------------------------------------------------
    | Génération Automatique
    |--------------------------------------------------------------------------
    |
    | Si true, le code est généré automatiquement à la création
    | Si false, le code doit être fourni manuellement
    |
    */

    'auto_generate' => env('ASSET_CODE_AUTO_GENERATE', true),

];
