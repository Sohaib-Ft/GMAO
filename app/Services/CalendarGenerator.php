<?php

namespace App\Services;

use DateTime;
use Illuminate\Support\Collection;

class CalendarGenerator
{
    private DateTime $currentMonth;
    private RruleParser $parser;

    public function __construct(?DateTime $date = null)
    {
        $this->currentMonth = $date ?? new DateTime('first day of this month');
    }

    /**
     * Génère un calendrier pour un mois avec les occurrences RRULE
     */
    public function generateMonth(string $rrule): array
    {
        $this->parser = new RruleParser($rrule);
        
        $startDate = $this->getMonthStart();
        $endDate = $this->getMonthEnd();
        $occurrences = $this->parser->getNextOccurrences($startDate, 100);
        
        // Filtrer les occurrences du mois
        $monthOccurrences = $this->filterOccurrencesInMonth($occurrences, $startDate, $endDate);
        
        // Générer la grille du calendrier
        return [
            'month' => $this->currentMonth->format('m'),
            'year' => $this->currentMonth->format('Y'),
            'month_name' => $this->currentMonth->format('F'),
            'weeks' => $this->generateWeeks($monthOccurrences),
            'occurrences' => $monthOccurrences,
            'summary' => $this->parser->toFrench(),
        ];
    }

    /**
     * Génère plusieurs mois
     */
    public function generateMonths(string $rrule, int $count = 3): array
    {
        $months = [];
        $date = clone $this->currentMonth;
        
        for ($i = 0; $i < $count; $i++) {
            $generator = new CalendarGenerator($date);
            $months[] = $generator->generateMonth($rrule);
            $date->modify('first day of next month');
        }
        
        return $months;
    }

    /**
     * Génère une année calendrier
     */
    public function generateYear(string $rrule): array
    {
        $months = [];
        $date = clone $this->currentMonth;
        $date->setDate($date->format('Y'), 1, 1);
        
        for ($i = 0; $i < 12; $i++) {
            $generator = new CalendarGenerator($date);
            $months[] = $generator->generateMonth($rrule);
            $date->modify('first day of next month');
        }
        
        return $months;
    }

    /**
     * Obtient le début du mois (lundi)
     */
    private function getMonthStart(): DateTime
    {
        $date = clone $this->currentMonth;
        $date->setTime(0, 0, 0);
        
        // Si le mois commence en milieu de semaine, revenir au lundi précédent
        $dayOfWeek = $date->format('N'); // 1=Lundi, 7=Dimanche
        if ($dayOfWeek > 1) {
            $date->modify('-' . ($dayOfWeek - 1) . ' days');
        }
        
        return $date;
    }

    /**
     * Obtient la fin du mois (dimanche)
     */
    private function getMonthEnd(): DateTime
    {
        $date = new DateTime('last day of this month', $this->currentMonth->getTimezone());
        $date->setDate($this->currentMonth->format('Y'), $this->currentMonth->format('m'), $this->currentMonth->format('t'));
        $date->setTime(23, 59, 59);
        
        // Aller au dimanche suivant si nécessaire
        $dayOfWeek = $date->format('N');
        if ($dayOfWeek < 7) {
            $date->modify('+' . (7 - $dayOfWeek) . ' days');
        }
        
        return $date;
    }

    /**
     * Filtre les occurrences qui se trouvent dans le mois
     */
    private function filterOccurrencesInMonth(array $occurrences, DateTime $start, DateTime $end): array
    {
        return array_filter($occurrences, function (DateTime $date) use ($start, $end) {
            return $date >= $start && $date <= $end;
        });
    }

    /**
     * Génère les semaines du calendrier
     */
    private function generateWeeks(array $occurrences): array
    {
        $weeks = [];
        $currentDate = $this->getMonthStart();
        $monthEnd = $this->getMonthEnd();
        
        // Créer une map date -> occurrence pour lookup rapide
        $occurrenceMap = [];
        foreach ($occurrences as $occurrence) {
            $key = $occurrence->format('Y-m-d');
            $occurrenceMap[$key][] = $occurrence;
        }
        
        $week = [];
        while ($currentDate <= $monthEnd) {
            $dateKey = $currentDate->format('Y-m-d');
            $isCurrentMonth = $currentDate->format('m') === $this->currentMonth->format('m');
            
            $week[] = [
                'date' => clone $currentDate,
                'day' => $currentDate->format('d'),
                'unix' => $currentDate->getTimestamp(),
                'is_current_month' => $isCurrentMonth,
                'day_of_week' => $currentDate->format('N'),
                'has_occurrence' => isset($occurrenceMap[$dateKey]),
                'occurrences' => $occurrenceMap[$dateKey] ?? [],
            ];
            
            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
            
            $currentDate->modify('+1 day');
        }
        
        return $weeks;
    }

    /**
     * Formate une date pour l'affichage
     */
    public static function formatDate(DateTime $date, string $format = 'd M Y'): string
    {
        return $date->format($format);
    }

    /**
     * Obtient le jour de la semaine en français
     */
    public static function getDayName(int $dayOfWeek): string
    {
        $days = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];
        
        return $days[$dayOfWeek] ?? '';
    }

    /**
     * Obtient le mois en français
     */
    public static function getMonthName(int $month): string
    {
        $months = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        return $months[$month - 1] ?? '';
    }

    /**
     * Passe au mois suivant
     */
    public function nextMonth(): self
    {
        $this->currentMonth->modify('first day of next month');
        return $this;
    }

    /**
     * Passe au mois précédent
     */
    public function previousMonth(): self
    {
        $this->currentMonth->modify('first day of previous month');
        return $this;
    }

    /**
     * Définit le mois/année
     */
    public function setMonth(int $month, int $year): self
    {
        $this->currentMonth->setDate($year, $month, 1);
        return $this;
    }

    /**
     * Obtient le mois actuel
     */
    public function getCurrentMonth(): DateTime
    {
        return clone $this->currentMonth;
    }
}
