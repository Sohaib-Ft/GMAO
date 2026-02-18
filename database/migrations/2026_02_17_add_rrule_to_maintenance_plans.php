<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute la colonne RRULE à la table maintenance_plans
     * 
     * Si votre table existe déjà, exécutez cette migration:
     * php artisan migrate
     * 
     * Si vous devez l'ajouter ultérieurement:
     * php artisan migrate --path=database/migrations/XXXX_XX_XX_add_rrule_to_maintenance_plans.php
     */
    public function up(): void
    {
        if (Schema::hasTable('maintenance_plans')) {
            Schema::table('maintenance_plans', function (Blueprint $table) {
                // Ajouter la colonne RRULE si elle n'existe pas
                if (!Schema::hasColumn('maintenance_plans', 'rrule')) {
                    $table->text('rrule')
                        ->nullable()
                        ->after('equipement_id')
                        ->comment('RFC 5545 RRULE format - ex: FREQ=WEEKLY;BYDAY=MO,WE,FR');
                }

                // Optionnel: Créer un index pour les performances
                // $table->index('rrule(10)');  // Indexe les 10 premiers caractères
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('maintenance_plans')) {
            Schema::table('maintenance_plans', function (Blueprint $table) {
                if (Schema::hasColumn('maintenance_plans', 'rrule')) {
                    $table->dropColumn('rrule');
                }
            });
        }
    }
};
