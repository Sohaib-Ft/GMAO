<?php

namespace Tests\Unit;

use App\Models\Equipement;
use App\Services\AssetCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_unique_codes_for_equipments()
    {
        $equipement1 = Equipement::create([
            'nom' => 'Écran Dell 24 pouces',
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        $equipement2 = Equipement::create([
            'nom' => 'Écran Dell 24 pouces', // Même nom !
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        // Les codes doivent être différents
        $this->assertNotEquals($equipement1->code, $equipement2->code);
        
        // Format attendu: ECR-ITC-USA-0001 et ECR-ITC-USA-0002
        $this->assertEquals('ECR-ITC-USA-0001', $equipement1->code);
        $this->assertEquals('ECR-ITC-USA-0002', $equipement2->code);
    }

    /** @test */
    public function it_generates_different_codes_for_different_departments()
    {
        $equipement1 = Equipement::create([
            'nom' => 'Écran',
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        $equipement2 = Equipement::create([
            'nom' => 'Écran',
            'categorie' => 'Écran',
            'departement' => 'Administration', // Département différent
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        // Les préfixes doivent être différents
        $this->assertStringStartsWith('ECR-ITC-', $equipement1->code);
        $this->assertStringStartsWith('ECR-ADM-', $equipement2->code);
    }

    /** @test */
    public function it_validates_code_format()
    {
        $generator = new AssetCodeGenerator();

        // Codes valides
        $this->assertTrue($generator->validate('ECR-ITC-USA-0001'));
        $this->assertTrue($generator->validate('MOT-PRD-USB-9999'));
        $this->assertTrue($generator->validate('LAP-ADM-BPR-24-0001'));

        // Codes invalides
        $this->assertFalse($generator->validate('INVALID'));
        $this->assertFalse($generator->validate('EC-ITC-USA-0001')); // Code catégorie trop court
        $this->assertFalse($generator->validate('ECR-IT-USA-0001')); // Code département trop court
        $this->assertFalse($generator->validate('ECR-ITC-US-0001')); // Code site trop court
        $this->assertFalse($generator->validate('ECR-ITC-USA-001')); // Numéro trop court
    }

    /** @test */
    public function it_handles_predefined_category_codes()
    {
        $equipement = Equipement::create([
            'nom' => 'Imprimante HP',
            'categorie' => 'Imprimante', // Code prédéfini: IMP
            'departement' => 'IT',
            'site' => 'Bureau Principal',
            'statut' => 'actif',
        ]);

        $this->assertStringStartsWith('IMP-', $equipement->code);
    }

    /** @test */
    public function it_handles_predefined_department_codes()
    {
        $equipement = Equipement::create([
            'nom' => 'Moteur',
            'categorie' => 'Moteur électrique',
            'departement' => 'Production', // Code prédéfini: PRD
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        $this->assertStringContains('-PRD-', $equipement->code);
    }

    /** @test */
    public function it_generates_codes_for_non_predefined_categories()
    {
        $equipement = Equipement::create([
            'nom' => 'Équipement Spécial',
            'categorie' => 'Custom Equipment', // Non prédéfini
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        // Devrait générer automatiquement un code (3 premières lettres)
        $this->assertMatchesRegularExpression('/^[A-Z]{3}-ITC-USA-[0-9]{4}$/', $equipement->code);
    }

    /** @test */
    public function it_increments_sequence_number_correctly()
    {
        // Créer 5 équipements identiques
        for ($i = 1; $i <= 5; $i++) {
            $equipement = Equipement::create([
                'nom' => 'Écran Test',
                'categorie' => 'Écran',
                'departement' => 'IT',
                'site' => 'Usine A',
                'statut' => 'actif',
            ]);

            $expectedCode = sprintf('ECR-ITC-USA-%04d', $i);
            $this->assertEquals($expectedCode, $equipement->code);
        }
    }

    /** @test */
    public function it_allows_manual_code_override()
    {
        $customCode = 'CUSTOM-CODE-001';
        
        $equipement = Equipement::create([
            'nom' => 'Équipement avec code manuel',
            'code' => $customCode, // Code fourni manuellement
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        $this->assertEquals($customCode, $equipement->code);
    }

    /** @test */
    public function it_provides_full_name_accessor()
    {
        $equipement = Equipement::create([
            'nom' => 'Écran Dell',
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        $expected = "{$equipement->code} - {$equipement->nom}";
        $this->assertEquals($expected, $equipement->full_name);
    }

    /** @test */
    public function it_filters_by_department()
    {
        Equipement::create([
            'nom' => 'Écran IT',
            'categorie' => 'Écran',
            'departement' => 'IT',
            'site' => 'Usine A',
            'statut' => 'actif',
        ]);

        Equipement::create([
            'nom' => 'Laptop Admin',
            'categorie' => 'Laptop',
            'departement' => 'Administration',
            'site' => 'Bureau Principal',
            'statut' => 'actif',
        ]);

        $itEquipements = Equipement::byDepartement('IT')->get();
        
        $this->assertCount(1, $itEquipements);
        $this->assertEquals('IT', $itEquipements->first()->departement);
    }
}
