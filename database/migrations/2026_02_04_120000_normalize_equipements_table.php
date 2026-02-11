<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create departments table
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create localisations table
        Schema::create('localisations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create equipement_serials table (one serial per equipment)
        Schema::create('equipement_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->string('serial')->nullable();
            $table->timestamps();
        });

        // Create equipement_years table (one-to-one)
        Schema::create('equipement_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade');
            $table->year('annee_fabrication')->nullable();
            $table->year('annee_acquisition')->nullable();
            $table->timestamps();
        });

        // Add foreign keys to equipements to reference localisation & departement
        Schema::table('equipements', function (Blueprint $table) {
            $table->foreignId('localisation_id')->nullable()->after('localisation')->constrained('localisations')->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->after('localisation_id')->constrained('departements')->nullOnDelete();
        });

        // Transfer existing data
        $equipements = DB::table('equipements')->get();

        foreach ($equipements as $eq) {
            // localisation -> localisations
            if (!empty($eq->localisation)) {
                $locId = DB::table('localisations')->where('name', $eq->localisation)->value('id');
                if (!$locId) {
                    $locId = DB::table('localisations')->insertGetId([
                        'name' => $eq->localisation,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('equipements')->where('id', $eq->id)->update(['localisation_id' => $locId]);
            }

            // departement (if column exists)
            if (property_exists($eq, 'departement') && !empty($eq->departement)) {
                $depId = DB::table('departements')->where('name', $eq->departement)->value('id');
                if (!$depId) {
                    $depId = DB::table('departements')->insertGetId([
                        'name' => $eq->departement,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('equipements')->where('id', $eq->id)->update(['departement_id' => $depId]);
            }

            // serial
            if (!empty($eq->numero_serie)) {
                DB::table('equipement_serials')->insert([
                    'equipement_id' => $eq->id,
                    'serial' => $eq->numero_serie,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // years
            if (property_exists($eq, 'annee_fabrication') || property_exists($eq, 'annee_acquisition')) {
                DB::table('equipement_years')->insert([
                    'equipement_id' => $eq->id,
                    'annee_fabrication' => property_exists($eq, 'annee_fabrication') ? $eq->annee_fabrication : null,
                    'annee_acquisition' => property_exists($eq, 'annee_acquisition') ? $eq->annee_acquisition : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Drop old columns (if they exist)
        Schema::table('equipements', function (Blueprint $table) {
            if (Schema::hasColumn('equipements', 'numero_serie')) {
                $table->dropColumn('numero_serie');
            }
            if (Schema::hasColumn('equipements', 'localisation')) {
                $table->dropColumn('localisation');
            }
            if (Schema::hasColumn('equipements', 'annee_fabrication')) {
                $table->dropColumn('annee_fabrication');
            }
            if (Schema::hasColumn('equipements', 'annee_acquisition')) {
                $table->dropColumn('annee_acquisition');
            }
            if (Schema::hasColumn('equipements', 'departement')) {
                $table->dropColumn('departement');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate removed columns (best-effort)
        Schema::table('equipements', function (Blueprint $table) {
            if (!Schema::hasColumn('equipements', 'numero_serie')) {
                $table->string('numero_serie')->nullable()->after('code');
            }
            if (!Schema::hasColumn('equipements', 'localisation')) {
                $table->string('localisation')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('equipements', 'annee_fabrication')) {
                $table->year('annee_fabrication')->nullable()->after('date_installation');
            }
            if (!Schema::hasColumn('equipements', 'annee_acquisition')) {
                $table->year('annee_acquisition')->nullable()->after('date_installation');
            }
            if (!Schema::hasColumn('equipements', 'departement')) {
                $table->string('departement')->nullable()->after('localisation');
            }
        });

        Schema::dropIfExists('equipement_years');
        Schema::dropIfExists('equipement_serials');
        Schema::dropIfExists('localisations');
        Schema::dropIfExists('departements');
    }
};
