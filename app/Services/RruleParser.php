<?php

namespace App\Services;

use DateTime;
use InvalidArgumentException;

class RruleParser
{
    private string $rrule;
    private array $parts;

    public function __construct(string $rrule)
    {
        if (!$this->isValidRrule($rrule)) {
            throw new InvalidArgumentException("Invalid RRULE format: {$rrule}");
        }
        
        $this->rrule = $rrule;
        $this->parts = $this->parseRrule($rrule);
    }

    /**
     * Valide que la RRULE suit le format RFC 5545
     */
    public static function isValidRrule(string $rrule): bool
    {
        return (bool) preg_match(config('rrule.regex_patterns.complete'), $rrule);
    }

    /**
     * Parse la chaîne RRULE en tableau associatif
     */
    private function parseRrule(string $rrule): array
    {
        $parts = [];
        foreach (explode(';', $rrule) as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', $part, 2);
                $parts[$key] = $value;
            }
        }
        return $parts;
    }

    /**
     * Obtient la fréquence (DAILY, WEEKLY, MONTHLY, YEARLY)
     */
    public function getFrequency(): string
    {
        return $this->parts['FREQ'] ?? 'DAILY';
    }

    /**
     * Obtient l'intervalle (1, 2, 3, ...)
     */
    public function getInterval(): int
    {
        return (int) ($this->parts['INTERVAL'] ?? 1);
    }

    /**
     * Obtient les jours de la semaine (pour WEEKLY)
     */
    public function getWeekdays(): array
    {
        if (!isset($this->parts['BYDAY'])) {
            return [];
        }
        return explode(',', $this->parts['BYDAY']);
    }

    /**
     * Obtient le jour du mois (pour MONTHLY)
     */
    public function getDayOfMonth(): ?int
    {
        if (!isset($this->parts['BYMONTHDAY'])) {
            return null;
        }
        return (int) $this->parts['BYMONTHDAY'];
    }

    /**
     * Obtient le jour relatif (pour MONTHLY relatif, ex: 2MO)
     */
    public function getRelativeDay(): ?string
    {
        if (!isset($this->parts['BYDAY']) || isset($this->parts['BYMONTHDAY'])) {
            return null;
        }
        return $this->parts['BYDAY'];
    }

    /**
     * Génère un résumé en français
     */
    public function toFrench(): string
    {
        $frequency = $this->getFrequency();
        $interval = $this->getInterval();
        
        $frequencyNames = [
            'DAILY' => 'chaque jour',
            'WEEKLY' => 'chaque semaine',
            'MONTHLY' => 'chaque mois',
            'YEARLY' => 'chaque année',
        ];

        $freq = $frequencyNames[$frequency] ?? $frequency;

        if ($interval > 1) {
            $unit = match ($frequency) {
                'DAILY' => 'jour',
                'WEEKLY' => 'semaine',
                'MONTHLY' => 'mois',
                'YEARLY' => 'année',
                default => 'période',
            };
            $freq = "tous les {$interval} {$unit}s";
        }

        $details = '';

        if ($frequency === 'WEEKLY') {
            $weekdays = $this->getWeekdays();
            if (!empty($weekdays)) {
                $dayNames = [
                    'MO' => 'lundi', 'TU' => 'mardi', 'WE' => 'mercredi', 'TH' => 'jeudi',
                    'FR' => 'vendredi', 'SA' => 'samedi', 'SU' => 'dimanche'
                ];
                $selectedDays = array_map(fn($d) => $dayNames[$d] ?? $d, $weekdays);
                $details = ' le ' . implode(' et ', $selectedDays);
            }
        } elseif ($frequency === 'MONTHLY') {
            $dayOfMonth = $this->getDayOfMonth();
            if ($dayOfMonth) {
                $suffix = $dayOfMonth === 1 ? 'er' : '';
                $details = " le {$dayOfMonth}{$suffix}";
            } else {
                $relativeDay = $this->getRelativeDay();
                if ($relativeDay) {
                    $details = $this->formatRelativeDayFrench($relativeDay);
                }
            }
        }

        return "Se répète {$freq}{$details}";
    }

    /**
     * Formate un jour relatif en français (ex: 2MO -> "le deuxième lundi")
     */
    private function formatRelativeDayFrench(string $relativeDay): string
    {
        $matches = [];
        if (!preg_match('/^(-?\d+)([A-Z]{2})$/', $relativeDay, $matches)) {
            return '';
        }

        $position = (int) $matches[1];
        $day = $matches[2];

        $positions = config('rrule.weekday_positions');
        $weekdays = config('rrule.weekdays');

        $positionName = $positions[$position] ?? "position {$position}";
        $dayName = $weekdays[$day]['long'] ?? $day;

        return " le {$positionName} " . strtolower($dayName);
    }

    /**
     * Génère les prochaines N occurrences
     * Note: Implémentation simple, à améliorer pour les cas complexes
     */
    public function getNextOccurrences(DateTime $startDate, int $count = 10): array
    {
        $occurrences = [];
        $current = clone $startDate;
        $frequency = $this->getFrequency();
        $interval = $this->getInterval();

        for ($i = 0; $i < $count; $i++) {
            $current = $this->getNextDate($current, $frequency, $interval);
            
            // Vérifier les contraintes (BYDAY, BYMONTHDAY, etc.)
            if ($this->matchesConstraints($current)) {
                $occurrences[] = clone $current;
            }
        }

        return $occurrences;
    }

    /**
     * Récupère la date suivante selon la fréquence
     */
    private function getNextDate(DateTime $date, string $frequency, int $interval): DateTime
    {
        $next = clone $date;
        
        switch ($frequency) {
            case 'DAILY':
                $next->modify("+{$interval} day");
                break;
            case 'WEEKLY':
                $next->modify("+{$interval} week");
                break;
            case 'MONTHLY':
                $next->modify("+{$interval} month");
                break;
            case 'YEARLY':
                $next->modify("+{$interval} year");
                break;
        }

        return $next;
    }

    /**
     * Vérifie si une date respecte les contraintes (BYDAY, BYMONTHDAY, etc.)
     */
    private function matchesConstraints(DateTime $date): bool
    {
        $frequency = $this->getFrequency();

        if ($frequency === 'WEEKLY') {
            $weekdays = $this->getWeekdays();
            if (!empty($weekdays)) {
                $dayOfWeek = $this->getDayOfWeekAbbr($date);
                if (!in_array($dayOfWeek, $weekdays)) {
                    return false;
                }
            }
        }

        if ($frequency === 'MONTHLY') {
            $dayOfMonth = $this->getDayOfMonth();
            if ($dayOfMonth !== null) {
                if ($date->format('d') != $dayOfMonth) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Récupère l'abréviation du jour de la semaine (MO, TU, etc.)
     */
    private function getDayOfWeekAbbr(DateTime $date): string
    {
        $abbreviations = [
            'Monday' => 'MO',
            'Tuesday' => 'TU',
            'Wednesday' => 'WE',
            'Thursday' => 'TH',
            'Friday' => 'FR',
            'Saturday' => 'SA',
            'Sunday' => 'SU',
        ];

        return $abbreviations[$date->format('l')] ?? '';
    }

    /**
     * Retourne la RRULE brute
     */
    public function toString(): string
    {
        return $this->rrule;
    }

    /**
     * Retourne toutes les parties de la RRULE
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Crée une RRULE à partir de paramètres
     */
    public static function build(
        string $frequency = 'WEEKLY',
        int $interval = 1,
        array $byday = [],
        ?int $bymonthday = null
    ): string {
        $parts = ["FREQ={$frequency}"];

        if ($interval > 1) {
            $parts[] = "INTERVAL={$interval}";
        }

        if (!empty($byday)) {
            $parts[] = 'BYDAY=' . implode(',', $byday);
        }

        if ($bymonthday !== null) {
            $parts[] = "BYMONTHDAY={$bymonthday}";
        }

        $rrule = implode(';', $parts);

        if (!self::isValidRrule($rrule)) {
            throw new InvalidArgumentException("Generated invalid RRULE: {$rrule}");
        }

        return $rrule;
    }
}
