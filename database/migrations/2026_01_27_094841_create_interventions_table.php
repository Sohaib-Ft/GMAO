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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();

            // Lien vers le work order
            $table->foreignId('work_order_id')
                ->constrained('work_order')
                ->onDelete('cascade');

            // Technicien qui effectue l'intervention
            $table->foreignId('technicien_id')
                ->constrained('users')
                ->onDelete('restrict'); // empêche la suppression du technicien


            // Détails de l'intervention
            $table->text('description');

            // Durée en minutes et coût en devise
            $table->integer('duree')->nullable(); // en minutes
            $table->decimal('cout', 10, 2)->default(0); // coût en monnaie locale

            // Dates début / fin automatique si nécessaire
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
