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
        Schema::create('work_order', function (Blueprint $table) {
            $table->id();

            // Employé qui crée le work order
            $table->foreignId('employe_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Technicien assigné
            $table->foreignId('technicien_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Équipement concerné
            $table->foreignId('equipement_id')
                ->constrained('equipements')
                ->onDelete('cascade');

            // Détails du work order
            $table->string('titre');
            $table->text('description');

            // Priorité et statut
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('statut', ['nouvelle', 'affectee', 'en_cours', 'terminee', 'annulee'])
                  ->default('nouvelle');

            // Dates et durée
            $table->dateTime('date_creation')->nullable();
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->integer('duree')->nullable(); // durée en minutes

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order');
    }
};
