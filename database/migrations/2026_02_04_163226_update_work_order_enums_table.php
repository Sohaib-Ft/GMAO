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
        Schema::table('work_order', function (Blueprint $table) {
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale')->change();
            $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'annulee'])->default('en_attente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne')->change();
            $table->enum('statut', ['nouvelle', 'affectee', 'en_cours', 'terminee', 'annulee'])->default('nouvelle')->change();
        });
    }
};
