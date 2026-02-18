<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;

class RecurrenceService
{
    /**
     * Calcule la prochaine occurrence après une date donnée basée sur une règle RRULE.
     * 
     * @param string $rrule La règle RRULE (ex: FREQ=WEEKLY;BYDAY=MO,WE)
     * @param Carbon|string $startDate La date de départ
     * @return Carbon|null
     */
    public function getNextOccurrence(string $rrule, $startDate)
    {
        $start = Carbon::parse($startDate);
        $rules = $this->parseRRule($rrule);

        if (!isset($rules['FREQ'])) return null;

        switch ($rules['FREQ']) {
            case 'DAILY':
                return $start->addDay();

            case 'WEEKLY':
                if (!isset($rules['BYDAY'])) return $start->addWeek();
                
                $days = explode(',', $rules['BYDAY']);
                $targetDays = $this->mapDaysToCarbon($days);
                
                // On cherche le prochain jour dans la semaine ou la semaine d'après
                for ($i = 1; $i <= 7; $i++) {
                    $candidate = $start->copy()->addDays($i);
                    if (in_array($candidate->dayOfWeek, $targetDays)) {
                        return $candidate;
                    }
                }
                return $start->addWeek();

            case 'MONTHLY':
                if (isset($rules['BYMONTHDAY'])) {
                    return $start->addMonth()->day((int)$rules['BYMONTHDAY']);
                }
                
                if (isset($rules['BYDAY']) && str_contains($rules['BYDAY'], '-1')) {
                    $dayStr = str_replace('-1', '', $rules['BYDAY']);
                    $carbonDay = $this->mapDayToCarbon($dayStr);
                    
                    // Dernier [Jour] du mois prochain
                    return (new Carbon("last " . $this->getDayName($carbonDay) . " of next month"));
                }
                
                return $start->addMonth();

            default:
                return $start->addMonth();
        }
    }

    /**
     * Génère une liste d'occurrences pour une période donnée (pour le calendrier).
     */
    public function getOccurrencesBetween(string $rrule, $startDate, $endDate)
    {
        $occurrences = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // On limite pour éviter les boucles infinies
        $maxSafe = 100;
        
        while ($current->lte($end) && $maxSafe > 0) {
            $occurrences[] = $current->copy();
            $next = $this->getNextOccurrence($rrule, $current);
            if (!$next || $next->equalTo($current)) break;
            $current = $next;
            $maxSafe--;
        }
        
        return $occurrences;
    }

    private function parseRRule($rrule)
    {
        $parts = explode(';', $rrule);
        $rules = [];
        foreach ($parts as $part) {
            $pair = explode('=', $part);
            if (count($pair) === 2) {
                $rules[$pair[0]] = $pair[1];
            }
        }
        return $rules;
    }

    private function mapDaysToCarbon(array $days)
    {
        $map = ['SU' => 0, 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6];
        return array_map(fn($d) => $map[$d], $days);
    }

    private function mapDayToCarbon(string $day)
    {
        $map = ['SU' => 0, 'MO' => 1, 'TU' => 2, 'WE' => 3, 'TH' => 4, 'FR' => 5, 'SA' => 6];
        return $map[$day];
    }

    private function getDayName($carbonDay)
    {
        return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][$carbonDay];
    }
}
