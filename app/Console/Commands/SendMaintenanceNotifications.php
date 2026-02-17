<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMaintenanceNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for maintenance plans due today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();
        
        $plans = \App\Models\MaintenancePlan::whereDate('prochaine_date', $today)
            ->where('statut', 'actif')
            ->whereNotNull('technicien_id')
            ->with(['technician', 'equipement'])
            ->get();

        $count = 0;

        foreach ($plans as $plan) {
            if ($plan->technician) {
                $plan->technician->notify(new \App\Notifications\MaintenanceDueNotification($plan));
                $count++;
            }
        }

        $this->info("Sent {$count} maintenance notifications.");
    }
}
