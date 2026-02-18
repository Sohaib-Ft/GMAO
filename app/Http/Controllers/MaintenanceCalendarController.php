<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use App\Models\MaintenancePlan;
use App\Models\User;
use App\Services\CalendarGenerator;
use App\Services\RruleParser;
use DateTime;
use Illuminate\Support\Facades\DB;

class MaintenanceCalendarController extends Controller
{
    public function index()
    {
        // Récupérer les paramètres de filtrage
        $equipementId = request('equipement');
        $technicianId = request('technicien');
        $type = request('type');
        $view = request('view', 'month');
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        // Récupérer les plans actifs
        $query = MaintenancePlan::query()
            ->where('statut', 'actif')
            ->with('equipement', 'technicien');

        if ($equipementId) {
            $query->where('equipement_id', $equipementId);
        }

        if ($technicianId) {
            $query->where('technicien_id', $technicianId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $plans = $query->get();

        // Générer les calendriers selon la vue
        $date = new DateTime("{$year}-{$month}-01");
        $generator = new CalendarGenerator($date);

        $calendars = match ($view) {
            'quarter' => $generator->generateMonths(null, 3),
            'year' => $generator->generateYear(null),
            default => [$generator->generateMonth(null)],
        };

        // Enrichir les calendriers avec les occurrences réelles
        $calendars = $this->enrichCalendars($calendars, $plans);

        // Calculer les prochaines maintenances
        $upcomingMaintenance = $this->getUpcomingMaintenance($plans, 30);

        // Calculer les statistiques
        $stats = $this->calculateStats($plans);

        return view('technician.maintenance-calendar', [
            'calendars' => $calendars,
            'upcomingMaintenance' => $upcomingMaintenance,
            'equipements' => Equipement::orderBy('nom')->get(),
            'technicians' => User::where('role', 'technician')->orderBy('name')->get(),
            'stats' => $stats,
        ]);
    }

    /**
     * Enrichit les calendriers avec les occurrences réelles des plans
     */
    private function enrichCalendars(array $calendars, $plans): array
    {
        foreach ($calendars as &$calendar) {
            $occurrences = [];

            // Pour chaque plan, générer les occurrences du mois
            foreach ($plans as $plan) {
                try {
                    $parser = new RruleParser($plan->rrule);
                    $monthStart = new DateTime("{$calendar['year']}-{$calendar['month']}-01");
                    $monthOccurrences = $parser->getNextOccurrences($monthStart, 100);

                    // Filtrer pour le mois actuel
                    $monthEnd = new DateTime('last day of this month', $monthStart->getTimezone());
                    $monthOccurrences = array_filter($monthOccurrences, function (DateTime $date) use ($monthStart, $monthEnd) {
                        return $date >= $monthStart && $date <= $monthEnd;
                    });

                    foreach ($monthOccurrences as $occurrence) {
                        $key = $occurrence->format('Y-m-d');
                        if (!isset($occurrences[$key])) {
                            $occurrences[$key] = [];
                        }
                        $occurrences[$key][] = [
                            'plan' => $plan,
                            'date' => $occurrence,
                        ];
                    }
                } catch (\Exception $e) {
                    // Ignorer les RRULE invalides
                    continue;
                }
            }

            // Mettre à jour les semaines avec les occurrences
            foreach ($calendar['weeks'] as &$week) {
                foreach ($week as &$day) {
                    $dateKey = $day['date']->format('Y-m-d');
                    if (isset($occurrences[$dateKey])) {
                        $day['has_occurrence'] = true;
                        $day['occurrences'] = $occurrences[$dateKey];
                    }
                }
            }

            $calendar['occurrences'] = array_values(array_merge(...array_values($occurrences)));
        }

        return $calendars;
    }

    /**
     * Récupère les prochaines maintenances
     */
    private function getUpcomingMaintenance($plans, int $days = 30): array
    {
        $upcoming = [];
        $now = new DateTime();
        $endDate = (clone $now)->modify("+{$days} days");

        foreach ($plans as $plan) {
            try {
                $parser = new RruleParser($plan->rrule);
                $occurrences = $parser->getNextOccurrences($now, 1000);

                foreach ($occurrences as $date) {
                    if ($date >= $now && $date <= $endDate) {
                        $upcoming[] = [
                            'plan' => $plan,
                            'equipement' => $plan->equipement,
                            'date' => $date,
                        ];
                    } elseif ($date > $endDate) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Trier par date
        usort($upcoming, function ($a, $b) {
            return $a['date']->getTimestamp() - $b['date']->getTimestamp();
        });

        return array_slice($upcoming, 0, 20);
    }

    /**
     * Calcule les statistiques
     */
    private function calculateStats($plans): array
    {
        $stats = [
            'total_plans' => $plans->count(),
            'active_plans' => $plans->where('statut', 'actif')->count(),
            'this_month_count' => 0,
            'next_30_days_count' => 0,
            'by_technician' => [],
        ];

        $now = new DateTime();
        $thisMonthEnd = new DateTime('last day of this month');
        $next30Days = (clone $now)->modify('+30 days');

        $monthCount = 0;
        $nextCount = 0;
        $byTech = [];

        foreach ($plans as $plan) {
            // Compter par technicien
            if ($plan->technicien) {
                $techName = $plan->technicien->name;
                $byTech[$techName] = ($byTech[$techName] ?? 0) + 1;
            }

            try {
                $parser = new RruleParser($plan->rrule);
                $occurrences = $parser->getNextOccurrences($now, 1000);

                foreach ($occurrences as $date) {
                    if ($date <= $thisMonthEnd && $date >= $now) {
                        $monthCount++;
                    }
                    if ($date <= $next30Days && $date >= $now) {
                        $nextCount++;
                    }
                    if ($date > $next30Days) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $stats['this_month_count'] = $monthCount;
        $stats['next_30_days_count'] = $nextCount;
        $stats['by_technician'] = $byTech;

        return $stats;
    }
}
