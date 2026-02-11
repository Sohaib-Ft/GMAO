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
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            
            // Infos form
            $table->string('nom');
           
            $table->string('email');
            $table->text('message')->nullable();

            // Link to user
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Workflow
            $table->enum('status', ['en_attente', 'traitee', 'refusee'])->default('en_attente');

            // Audit
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
