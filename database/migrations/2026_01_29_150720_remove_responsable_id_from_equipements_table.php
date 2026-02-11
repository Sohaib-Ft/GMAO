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
            if (Schema::hasColumn('equipements', 'responsable_id')) {
                // We need to drop the foreign key first if it exists, but since we don't know the exact name auto-generated,
                // we'll try to drop the column directly. If standard foreign key constraints are rigorous, 
                // we might need to drop the constrained key first. 
                // Laravel's dropConstrainedForeignId handles this if formatted correctly, 
                // but dropColumn might fail if FK exists and is not dropped.
                // Let's try flexible approach.
                $table->dropForeign(['responsable_id']);
                $table->dropColumn('responsable_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            $table->foreignId('responsable_id')->nullable()->after('localisation')->constrained('users')->onDelete('set null');
        });
    }
};
