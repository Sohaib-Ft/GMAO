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
        Schema::create('equipements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique();
            $table->string('numero_serie')->nullable();
            $table->string('marque')->nullable();
            $table->string('modele')->nullable();
            $table->string('statut')->default('actif'); // actif, inactif, maintenance, panne
            $table->string('localisation')->nullable(); // Emplacement
            $table->foreignId('responsable_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Dates & Info Tech
            $table->date('date_installation')->nullable();
            $table->year('annee_fabrication')->nullable();
            $table->date('date_fin_garantie')->nullable();
            
            // Media
            $table->string('image_path')->nullable();
            $table->string('manuel_path')->nullable(); // PDF
            
            // Stats
            $table->integer('compteur_heures')->default(0); # Heures de fonctionnement
            
            // Metadata
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipements');
    }
};
