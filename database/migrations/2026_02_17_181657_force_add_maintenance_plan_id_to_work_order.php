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
        // Check if column exists before adding to avoid duplicate column error if run multiple times
        if (!Schema::hasColumn('work_order', 'maintenance_plan_id')) {
            Schema::table('work_order', function (Blueprint $table) {
                $table->foreignId('maintenance_plan_id')->nullable()->after('equipement_id')->constrained('maintenance_plans')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order', function (Blueprint $table) {
            // Only drop if exists
            if (Schema::hasColumn('work_order', 'maintenance_plan_id')) {
                $table->dropConstrainedForeignId('maintenance_plan_id');
            }
        });
    }
};
