<?php

return [
    /**
     * Configuration pour la génération et validation des règles RRULE (RFC 5545)
     */

    'frequencies' => [
        'DAILY' => [
            'label' => 'Quotidien',
            'short' => 'Jour',
            'icon' => 'bx-sun',
        ],
        'WEEKLY' => [
            'label' => 'Hebdomadaire',
            'short' => 'Semaine',
            'icon' => 'bx-calendar',
        ],
        'MONTHLY' => [
            'label' => 'Mensuel',
            'short' => 'Mois',
            'icon' => 'bx-calendar-heart',
        ],
        'YEARLY' => [
            'label' => 'Annuel',
            'short' => 'Année',
            'icon' => 'bx-calendar-year',
        ],
    ],

    'weekdays' => [
        'MO' => ['short' => 'L', 'long' => 'Lundi', 'english' => 'Monday'],
        'TU' => ['short' => 'M', 'long' => 'Mardi', 'english' => 'Tuesday'],
        'WE' => ['short' => 'M', 'long' => 'Mercredi', 'english' => 'Wednesday'],
        'TH' => ['short' => 'J', 'long' => 'Jeudi', 'english' => 'Thursday'],
        'FR' => ['short' => 'V', 'long' => 'Vendredi', 'english' => 'Friday'],
        'SA' => ['short' => 'S', 'long' => 'Samedi', 'english' => 'Saturday'],
        'SU' => ['short' => 'D', 'long' => 'Dimanche', 'english' => 'Sunday'],
    ],

    'weekday_positions' => [
        '1' => 'Premier',
        '2' => 'Deuxième',
        '3' => 'Troisième',
        '4' => 'Quatrième',
        '-1' => 'Dernier',
    ],

    'day_groups' => [
        'MO,TU,WE,TH,FR' => 'Jours de semaine',
        'SA,SU' => 'Jours de weekend',
    ],

    /**
     * Jours par défaut lors de la création
     */
    'default_weekdays' => ['MO', 'WE', 'FR'],

    /**
     * Intervalle par défaut
     */
    'default_interval' => 1,

    /**
     * Jour du mois par défaut (1-31)
     */
    'default_day_of_month' => 1,

    /**
     * Patterns courants pour validation rapide
     */
    'regex_patterns' => [
        'base' => '/^FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)/',
        'complete' => '/^FREQ=(DAILY|WEEKLY|MONTHLY|YEARLY)(;(INTERVAL|BYDAY|BYMONTHDAY)=[^;]*)*$/',
    ],

    /**
     * Exemples prédéfinis pour les utilisateurs
     */
    'presets' => [
        [
            'label' => 'Quotidiennement',
            'rrule' => 'FREQ=DAILY',
            'description' => 'Se répète chaque jour',
        ],
        [
            'label' => 'Tous les 2 jours',
            'rrule' => 'FREQ=DAILY;INTERVAL=2',
            'description' => 'Se répète tous les 2 jours',
        ],
        [
            'label' => 'Hebdomadaire (L-M-M-J-V)',
            'rrule' => 'FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR',
            'description' => 'Se répète du lundi au vendredi',
        ],
        [
            'label' => 'Tous les lundis',
            'rrule' => 'FREQ=WEEKLY;BYDAY=MO',
            'description' => 'Se répète chaque lundi',
        ],
        [
            'label' => 'Le 1er et le 15 du mois',
            'rrule' => 'FREQ=MONTHLY;BYMONTHDAY=1,15',
            'description' => 'Se répète le 1er et le 15',
        ],
        [
            'label' => 'Le 1er lundi de chaque mois',
            'rrule' => 'FREQ=MONTHLY;BYDAY=1MO',
            'description' => 'Se répète le premier lundi',
        ],
        [
            'label' => 'Tous les 3 mois',
            'rrule' => 'FREQ=MONTHLY;INTERVAL=3;BYMONTHDAY=1',
            'description' => 'Révision trimestrielle',
        ],
        [
            'label' => 'Annuellement',
            'rrule' => 'FREQ=YEARLY',
            'description' => 'Se répète chaque année',
        ],
    ],

    /**
     * Limites de validation
     */
    'constraints' => [
        'interval_min' => 1,
        'interval_max' => 99,
        'day_of_month_min' => 1,
        'day_of_month_max' => 31,
    ],
];
