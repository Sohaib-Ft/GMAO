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
        // 1. Add equipment_type_id column
        Schema::table('equipements', function (Blueprint $table) {
            $table->foreignId('equipment_type_id')->nullable()->after('nom')->constrained('equipment_types')->nullOnDelete();
        });

        // 2. Transfer existing data
        $equipements = DB::table('equipements')->get();
        foreach ($equipements as $eq) {
            if (property_exists($eq, 'type_equipement') && !empty($eq->type_equipement)) {
                $typeId = DB::table('equipment_types')->where('name', $eq->type_equipement)->value('id');
                if (!$typeId) {
                    $typeId = DB::table('equipment_types')->insertGetId([
                        'name' => $eq->type_equipement,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                DB::table('equipements')->where('id', $eq->id)->update(['equipment_type_id' => $typeId]);
            }
        }

        // 3. Drop old column
        Schema::table('equipements', function (Blueprint $table) {
            if (Schema::hasColumn('equipements', 'type_equipement')) {
                $table->dropColumn('type_equipement');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipements', function (Blueprint $table) {
            if (!Schema::hasColumn('equipements', 'type_equipement')) {
                $table->string('type_equipement')->nullable()->after('nom');
            }
        });

        // Transfer back (optional but good practice)
        $equipements = DB::table('equipements')->get();
        foreach ($equipements as $eq) {
            if ($eq->equipment_type_id) {
                $typeName = DB::table('equipment_types')->where('id', $eq->equipment_type_id)->value('name');
                DB::table('equipements')->where('id', $eq->id)->update(['type_equipement' => $typeName]);
            }
        }

        Schema::table('equipements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_type_id');
        });
    }
};
