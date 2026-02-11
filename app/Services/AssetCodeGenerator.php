<?php

namespace App\Services;

use App\Models\Equipement;
use Illuminate\Support\Str;

class AssetCodeGenerator
{
    /**
     * Codes standardisés pour les catégories d'équipements
     */
    private const CATEGORY_CODES = [
        // Informatique
        'Écran' => 'ECR',
        'Ordinateur' => 'ORD',
        'Imprimante' => 'IMP',
        'Serveur' => 'SRV',
        'Switch' => 'SWT',
        'Routeur' => 'ROU',
        'Scanner' => 'SCN',
        'Laptop' => 'LAP',
        
        // Électrique
        'Transformateur' => 'TRF',
        'Générateur' => 'GEN',
        'Onduleur' => 'OND',
        'Tableau électrique' => 'TAB',
        'Moteur électrique' => 'MOT',
        
        // Mécanique
        'Pompe' => 'PMP',
        'Compresseur' => 'CPR',
        'Ventilateur' => 'VEN',
        'Convoyeur' => 'CNV',
        'Presse' => 'PRS',
        'Tour' => 'TOU',
        'Fraiseuse' => 'FRA',
        
        // Climatisation/Chauffage
        'Climatiseur' => 'CLM',
        'Chaudière' => 'CHD',
        'Radiateur' => 'RAD',
        
        // Autres
        'Véhicule' => 'VEH',
        'Mobilier' => 'MOB',
        'Outil' => 'OUT',
    ];

    /**
     * Codes standardisés pour les départements
     */
    private const DEPARTMENT_CODES = [
        'Production' => 'PRD',
        'Maintenance' => 'MNT',
        'IT' => 'ITC',
        'Informatique' => 'ITC',
        'Logistique' => 'LOG',
        'Qualité' => 'QLT',
        'Administration' => 'ADM',
        'RH' => 'RHU',
        'Ressources Humaines' => 'RHU',
        'Sécurité' => 'SEC',
        'Magasin' => 'MAG',
        'Atelier' => 'ATL',
        'Bureau' => 'BUR',
        'Entrepôt' => 'ENT',
    ];

    /**
     * Codes standardisés pour les sites
     */
    private const SITE_CODES = [
        'Usine A' => 'USA',
        'Usine B' => 'USB',
        'Bureau Principal' => 'BPR',
        'Entrepôt 1' => 'EN1',
        'Entrepôt 2' => 'EN2',
        'Site Nord' => 'SNO',
        'Site Sud' => 'SUD',
        'Site Est' => 'EST',
        'Site Ouest' => 'OUE',
    ];

    /**
     * Génère un code unique pour un équipement
     * 
     * Format: CAT-DEP-SITE-NNNN
     * Exemple: ECR-ITC-USA-0001 (Écran dans IT à l'Usine A)
     * 
     * @param Equipement $equipement
     * @return string
     */
    public function generate(Equipement $equipement): string
    {
        $categoryCode = $this->getCategoryCode($equipement->categorie);
        $departmentCode = $this->getDepartmentCode($equipement->departement);
        $siteCode = $this->getSiteCode($equipement->site);
        $sequenceNumber = $this->getNextSequenceNumber($categoryCode, $departmentCode, $siteCode);

        return "{$categoryCode}-{$departmentCode}-{$siteCode}-{$sequenceNumber}";
    }

    /**
     * Génère un code avec année d'acquisition (optionnel)
     * 
     * Format: CAT-DEP-SITE-YY-NNNN
     * Exemple: ECR-ITC-USA-24-0001
     * 
     * @param Equipement $equipement
     * @param bool $includeYear
     * @return string
     */
    public function generateWithYear(Equipement $equipement, bool $includeYear = false): string
    {
        if (!$includeYear || !$equipement->annee_acquisition) {
            return $this->generate($equipement);
        }

        $categoryCode = $this->getCategoryCode($equipement->categorie);
        $departmentCode = $this->getDepartmentCode($equipement->departement);
        $siteCode = $this->getSiteCode($equipement->site);
        $yearCode = substr($equipement->annee_acquisition, -2); // Derniers 2 chiffres
        $sequenceNumber = $this->getNextSequenceNumber($categoryCode, $departmentCode, $siteCode, $yearCode);

        return "{$categoryCode}-{$departmentCode}-{$siteCode}-{$yearCode}-{$sequenceNumber}";
    }

    /**
     * Obtient le code de catégorie (3 lettres)
     */
    private function getCategoryCode(?string $categorie): string
    {
        if (!$categorie) {
            return 'GEN'; // Generic
        }

        // Chercher dans le mapping prédéfini
        if (isset(self::CATEGORY_CODES[$categorie])) {
            return self::CATEGORY_CODES[$categorie];
        }

        // Générer automatiquement à partir du nom (3 premières lettres en majuscules)
        return strtoupper(substr(Str::slug($categorie, ''), 0, 3));
    }

    /**
     * Obtient le code de département (3 lettres)
     */
    private function getDepartmentCode(?string $departement): string
    {
        if (!$departement) {
            return 'GEN';
        }

        if (isset(self::DEPARTMENT_CODES[$departement])) {
            return self::DEPARTMENT_CODES[$departement];
        }

        return strtoupper(substr(Str::slug($departement, ''), 0, 3));
    }

    /**
     * Obtient le code de site (3 lettres)
     */
    private function getSiteCode(?string $site): string
    {
        if (!$site) {
            return 'DEF'; // Default
        }

        if (isset(self::SITE_CODES[$site])) {
            return self::SITE_CODES[$site];
        }

        return strtoupper(substr(Str::slug($site, ''), 0, 3));
    }

    /**
     * Obtient le prochain numéro de séquence (4 chiffres)
     */
    private function getNextSequenceNumber(
        string $categoryCode, 
        string $departmentCode, 
        string $siteCode,
        ?string $yearCode = null
    ): string {
        $prefix = $yearCode 
            ? "{$categoryCode}-{$departmentCode}-{$siteCode}-{$yearCode}-"
            : "{$categoryCode}-{$departmentCode}-{$siteCode}-";

        // Trouver le dernier code avec ce préfixe
        $lastEquipement = Equipement::where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastEquipement) {
            return '0001';
        }

        // Extraire le numéro de séquence du dernier code
        $lastCode = $lastEquipement->code;
        $lastNumber = (int) substr($lastCode, strrpos($lastCode, '-') + 1);
        
        $nextNumber = $lastNumber + 1;
        
        return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Valide un code d'équipement
     */
    public function validate(string $code): bool
    {
        // Format standard: XXX-YYY-ZZZ-NNNN
        // Format avec année: XXX-YYY-ZZZ-YY-NNNN
        $pattern = '/^[A-Z]{3}-[A-Z]{3}-[A-Z]{3}-(\d{2}-)?[0-9]{4}$/';
        
        return preg_match($pattern, $code) === 1;
    }

    /**
     * Obtient tous les codes de catégories disponibles
     */
    public static function getAvailableCategoryCodes(): array
    {
        return self::CATEGORY_CODES;
    }

    /**
     * Obtient tous les codes de départements disponibles
     */
    public static function getAvailableDepartmentCodes(): array
    {
        return self::DEPARTMENT_CODES;
    }

    /**
     * Obtient tous les codes de sites disponibles
     */
    public static function getAvailableSiteCodes(): array
    {
        return self::SITE_CODES;
    }
}
