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
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->text('rrule')->nullable()->after('frequence');
            $table->string('frequence')->nullable()->change();
            $table->integer('interval_jours')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_plans', function (Blueprint $table) {
            $table->dropColumn('rrule');
            $table->string('frequence')->nullable(false)->change();
            $table->integer('interval_jours')->nullable(false)->change();
        });
    }
};
