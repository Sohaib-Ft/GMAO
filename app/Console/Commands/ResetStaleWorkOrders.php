<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetStaleWorkOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workorders:reset-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remet en circulation les bons de travail assignés mais non démarrés depuis plus de 48h';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // On cherche les BT qui :
        // 1. Sont en attente (pas encore démarrés)
        // 2. Ont déjà un technicien assigné
        // 3. Ne sont pas liés à un plan de maintenance (correctif uniquement)
        // 4. N'ont pas bougé depuis 48h
        $staleOrders = \App\Models\WorkOrder::where('statut', 'en_attente')
            ->whereNotNull('technicien_id')
            ->whereNull('maintenance_plan_id')
            ->where('updated_at', '<', now()->subHours(48))
            ->get();

        $count = $staleOrders->count();

        foreach ($staleOrders as $order) {
            $order->update([
                'statut' => 'en_attente',
                'technicien_id' => null,
            ]);
        }

        if ($count > 0) {
            $this->info("{$count} bons de travail stagnants ont été remis en circulation (désassignés).");
        }
    }
}
