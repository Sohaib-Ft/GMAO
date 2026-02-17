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
        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->enum('type', ['preventive', 'corrective']);
            $table->enum('frequence', ['mensuelle', 'trimestrielle', 'annuelle'])->nullable();
            $table->integer('interval_jours')->nullable();
            $table->date('derniere_date')->nullable();
            $table->date('prochaine_date')->nullable();
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
