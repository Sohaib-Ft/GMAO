<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            // Remove old column if it exists
            if (Schema::hasColumn('equipements', 'etat')) {
                $table->dropColumn('etat');
            }

            // Add new columns
            $table->string('numero_serie')->nullable()->after('code');
            $table->string('marque')->nullable()->after('numero_serie');
            $table->string('modele')->nullable()->after('marque');
            
            // Add statut if not exists
            if (!Schema::hasColumn('equipements', 'statut')) {
                $table->string('statut')->default('actif')->after('modele');
            }
            
            // Localisation is already there, make it nullable if needed (requires dbal, skipping change for now, just assuming it's ok)
            
            $table->foreignId('responsable_id')->nullable()->after('localisation')->constrained('users')->onDelete('set null');
            
            // Dates & Info Tech
            $table->date('date_installation')->nullable()->after('responsable_id');
            $table->year('annee_fabrication')->nullable()->after('date_installation');
            $table->date('date_fin_garantie')->nullable()->after('annee_fabrication');
            
            // Media
            $table->string('image_path')->nullable()->after('date_fin_garantie');
            $table->string('manuel_path')->nullable()->after('image_path');
            
            // Stats
            $table->integer('compteur_heures')->default(0)->after('manuel_path');
            
            // Metadata
            $table->text('notes')->nullable()->after('compteur_heures');
            $table->json('tags')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->enum('etat', ['bon', 'en_panne', 'maintenance'])->default('bon');
            $table->dropForeign(['responsable_id']);
            $table->dropColumn([
                'numero_serie', 'marque', 'modele', 'statut', 'responsable_id',
                'date_installation', 'annee_fabrication', 'date_fin_garantie',
                'image_path', 'manuel_path', 'compteur_heures', 'notes', 'tags'
            ]);
        });
    }
};
