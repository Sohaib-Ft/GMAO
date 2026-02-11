<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STATISTIQUES WORKORDER ===\n\n";

// Comptage par priorité
echo "Comptage par priorité:\n";
$priorities = DB::table('work_order')
    ->select('priorite', DB::raw('count(*) as count'))
    ->groupBy('priorite')
    ->get();

foreach ($priorities as $p) {
    echo "  - {$p->priorite}: {$p->count}\n";
}

echo "\nComptage par statut:\n";
$statuts = DB::table('work_order')
    ->select('statut', DB::raw('count(*) as count'))
    ->groupBy('statut')
    ->get();

foreach ($statuts as $s) {
    echo "  - {$s->statut}: {$s->count}\n";
}

echo "\n=== COMPTEURS DASHBOARD ===\n\n";
echo "Demandes en attente (nouvelle + affectee): " . 
    DB::table('work_order')->whereIn('statut', ['nouvelle', 'affectee'])->count() . "\n";
echo "Tâches en cours: " . 
    DB::table('work_order')->where('statut', 'en_cours')->count() . "\n";
echo "Pannes critiques (priorite=urgent, statut!=terminee): " . 
    DB::table('work_order')->where('priorite', 'urgent')->where('statut', '!=', 'terminee')->count() . "\n";
