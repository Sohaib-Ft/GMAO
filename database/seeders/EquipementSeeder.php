<?php

namespace Database\Seeders;

use App\Models\Equipement;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier qu'il y a au moins un utilisateur
        $responsable = User::first();
        
        if (!$responsable) {
            $this->command->error('Veuillez créer au moins un utilisateur avant de seeder les équipements');
            return;
        }

        $this->command->info('🏭 Création des équipements...');

        // ============================================
        // DÉPARTEMENT IT - USINE A
        // ============================================
        $this->command->info('📱 Équipements IT - Usine A...');

        // Écrans (plusieurs avec le même nom)
        Equipement::create([
            'nom' => 'Écran Dell 24 pouces',
            'categorie' => 'Écran',
            'type_equipement' => 'Informatique',
            'departement' => 'IT',
            'site' => 'Usine A',
            'marque' => 'Dell',
            'modele' => 'P2422H',
            'numero_serie' => 'DELL001',
            'annee_acquisition' => 2024,
            'statut' => 'actif',
            'localisation' => 'Bureau 201',
            'responsable_id' => $responsable->id,
        ]); // Code: ECR-ITC-USA-0001

        Equipement::create([
            'nom' => 'Écran Dell 24 pouces',
            'categorie' => 'Écran',
            'type_equipement' => 'Informatique',
            'departement' => 'IT',
            'site' => 'Usine A',
            'marque' => 'Dell',
            'modele' => 'P2422H',
            'numero_serie' => 'DELL002',
            'annee_acquisition' => 2024,
            'statut' => 'actif',
            'localisation' => 'Bureau 202',
            'responsable_id' => $responsable->id,
        ]); // Code: ECR-ITC-USA-0002

        Equipement::create([
            'nom' => 'Écran Dell 24 pouces',
            'categorie' => 'Écran',
            'type_equipement' => 'Informatique',
            'departement' => 'IT',
            'site' => 'Usine A',
            'marque' => 'Dell',
            'modele' => 'P2422H',
            'numero_serie' => 'DELL003',
            'annee_acquisition' => 2025,
            'statut' => 'actif',
            'localisation' => 'Bureau 203',
            'responsable_id' => $responsable->id,
        ]); // Code: ECR-ITC-USA-0003

        // Laptops
        Equipement::create([
            'nom' => 'Laptop Lenovo ThinkPad',
            'categorie' => 'Laptop',
            'type_equipement' => 'Informatique',
            'departement' => 'IT',
            'site' => 'Usine A',
            'marque' => 'Lenovo',
            'modele' => 'ThinkPad T14',
            'numero_serie' => 'LNV789456',
            'annee_acquisition' => 2025,
            'statut' => 'actif',
            'localisation' => 'Bureau IT',
            'responsable_id' => $responsable->id,
        ]); // Code: LAP-ITC-USA-0001

        // Imprimantes
        Equipement::create([
            'nom' => 'Imprimante HP LaserJet',
            'categorie' => 'Imprimante',
            'type_equipement' => 'Informatique',
            'departement' => 'IT',
            'site' => 'Bureau Principal',
            'marque' => 'HP',
            'modele' => 'LaserJet Pro M404dn',
            'numero_serie' => 'HP123456',
            'annee_acquisition' => 2023,
            'statut' => 'actif',
            'localisation' => 'Salle d\'impression',
            'responsable_id' => $responsable->id,
        ]); // Code: IMP-ITC-BPR-0001

        // ============================================
        // DÉPARTEMENT PRODUCTION - USINE A
        // ============================================
        $this->command->info('🏭 Équipements Production - Usine A...');

        // Moteurs (plusieurs avec le même nom)
        Equipement::create([
            'nom' => 'Moteur électrique 5kW',
            'categorie' => 'Moteur électrique',
            'type_equipement' => 'Électrique',
            'departement' => 'Production',
            'site' => 'Usine A',
            'marque' => 'ABB',
            'modele' => 'M2BA 132S',
            'numero_serie' => 'ABB001',
            'annee_acquisition' => 2022,
            'statut' => 'actif',
            'localisation' => 'Ligne 1',
            'compteur_heures' => 12500,
            'responsable_id' => $responsable->id,
        ]); // Code: MOT-PRD-USA-0001

        Equipement::create([
            'nom' => 'Moteur électrique 5kW',
            'categorie' => 'Moteur électrique',
            'type_equipement' => 'Électrique',
            'departement' => 'Production',
            'site' => 'Usine A',
            'marque' => 'ABB',
            'modele' => 'M2BA 132S',
            'numero_serie' => 'ABB002',
            'annee_acquisition' => 2022,
            'statut' => 'actif',
            'localisation' => 'Ligne 2',
            'compteur_heures' => 11800,
            'responsable_id' => $responsable->id,
        ]); // Code: MOT-PRD-USA-0002

        // Compresseurs
        Equipement::create([
            'nom' => 'Compresseur Atlas Copco',
            'categorie' => 'Compresseur',
            'type_equipement' => 'Mécanique',
            'departement' => 'Production',
            'site' => 'Usine A',
            'marque' => 'Atlas Copco',
            'modele' => 'GA 30',
            'numero_serie' => 'AC789123',
            'annee_acquisition' => 2021,
            'statut' => 'actif',
            'localisation' => 'Salle des machines',
            'compteur_heures' => 25000,
            'responsable_id' => $responsable->id,
        ]); // Code: CPR-PRD-USA-0001

        // Pompes
        Equipement::create([
            'nom' => 'Pompe centrifuge',
            'categorie' => 'Pompe',
            'type_equipement' => 'Mécanique',
            'departement' => 'Production',
            'site' => 'Usine A',
            'marque' => 'Grundfos',
            'modele' => 'CR 5-8',
            'numero_serie' => 'GRU456789',
            'annee_acquisition' => 2023,
            'statut' => 'actif',
            'localisation' => 'Station de pompage',
            'compteur_heures' => 8500,
            'responsable_id' => $responsable->id,
        ]); // Code: PMP-PRD-USA-0001

        // Convoyeurs
        Equipement::create([
            'nom' => 'Convoyeur à bande 10m',
            'categorie' => 'Convoyeur',
            'type_equipement' => 'Mécanique',
            'departement' => 'Production',
            'site' => 'Usine A',
            'marque' => 'Flexco',
            'modele' => 'CB-1000',
            'numero_serie' => 'FLX001',
            'annee_acquisition' => 2020,
            'statut' => 'actif',
            'localisation' => 'Zone d\'expédition',
            'compteur_heures' => 35000,
            'responsable_id' => $responsable->id,
        ]); // Code: CNV-PRD-USA-0001

        // ============================================
        // DÉPARTEMENT PRODUCTION - USINE B
        // ============================================
        $this->command->info('🏭 Équipements Production - Usine B...');

        Equipement::create([
            'nom' => 'Compresseur Atlas Copco',
            'categorie' => 'Compresseur',
            'type_equipement' => 'Mécanique',
            'departement' => 'Production',
            'site' => 'Usine B',
            'marque' => 'Atlas Copco',
            'modele' => 'GA 45',
            'numero_serie' => 'AC999888',
            'annee_acquisition' => 2024,
            'statut' => 'actif',
            'localisation' => 'Salle des machines',
            'compteur_heures' => 3500,
            'responsable_id' => $responsable->id,
        ]); // Code: CPR-PRD-USB-0001

        // ============================================
        // DÉPARTEMENT MAINTENANCE - ATELIER
        // ============================================
        $this->command->info('🔧 Équipements Maintenance - Atelier...');

        Equipement::create([
            'nom' => 'Tour CNC',
            'categorie' => 'Tour',
            'type_equipement' => 'Mécanique',
            'departement' => 'Atelier',
            'site' => 'Usine A',
            'marque' => 'Haas',
            'modele' => 'ST-10',
            'numero_serie' => 'HAAS123',
            'annee_acquisition' => 2022,
            'statut' => 'actif',
            'localisation' => 'Atelier mécanique',
            'compteur_heures' => 5000,
            'responsable_id' => $responsable->id,
        ]); // Code: TOU-ATL-USA-0001

        Equipement::create([
            'nom' => 'Fraiseuse conventionnelle',
            'categorie' => 'Fraiseuse',
            'type_equipement' => 'Mécanique',
            'departement' => 'Atelier',
            'site' => 'Usine A',
            'marque' => 'Bridgeport',
            'modele' => 'Series I',
            'numero_serie' => 'BRI456',
            'annee_acquisition' => 2018,
            'statut' => 'actif',
            'localisation' => 'Atelier mécanique',
            'compteur_heures' => 15000,
            'responsable_id' => $responsable->id,
        ]); // Code: FRA-ATL-USA-0001

        // ============================================
        // DÉPARTEMENT ADMINISTRATION
        // ============================================
        $this->command->info('💼 Équipements Administration...');

        Equipement::create([
            'nom' => 'Laptop Dell Latitude',
            'categorie' => 'Laptop',
            'type_equipement' => 'Informatique',
            'departement' => 'Administration',
            'site' => 'Bureau Principal',
            'marque' => 'Dell',
            'modele' => 'Latitude 5520',
            'numero_serie' => 'DELL789',
            'annee_acquisition' => 2025,
            'statut' => 'actif',
            'localisation' => 'Bureau Direction',
            'responsable_id' => $responsable->id,
        ]); // Code: LAP-ADM-BPR-0001

        Equipement::create([
            'nom' => 'Climatiseur Daikin',
            'categorie' => 'Climatiseur',
            'type_equipement' => 'Climatisation',
            'departement' => 'Administration',
            'site' => 'Bureau Principal',
            'marque' => 'Daikin',
            'modele' => 'FTXM35R',
            'numero_serie' => 'DAI456',
            'annee_acquisition' => 2023,
            'statut' => 'actif',
            'localisation' => 'Salle de réunion',
            'responsable_id' => $responsable->id,
        ]); // Code: CLM-ADM-BPR-0001

        $this->command->info('✅ Seeding terminé ! ' . Equipement::count() . ' équipements créés.');
        
        // Afficher quelques exemples
        $this->command->newLine();
        $this->command->info('📋 Exemples de codes générés:');
        Equipement::orderBy('code')->take(5)->get()->each(function($eq) {
            $this->command->line("  {$eq->code} - {$eq->nom}");
        });
    }
}
