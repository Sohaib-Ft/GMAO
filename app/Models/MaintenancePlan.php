<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipement_id',
        'type',
        'frequence',
        'rrule',
        'derniere_date',
        'prochaine_date',
        'statut',
        'technicien_id',
    ];

    /**
     * Calcule et met à jour la prochaine date de maintenance basée sur l'RRULE.
     */
    public function updateNextDate($fromDate = null)
    {
        if (!$this->rrule) {
             // Fallback to simple interval if no RRULE
             if ($this->interval_jours) {
                 $this->prochaine_date = ($fromDate ?: now())->addDays((int)$this->interval_jours);
                 return $this->save();
             }
             return false;
        }

        $service = new \App\Services\RecurrenceService();
        $this->prochaine_date = $service->getNextOccurrence($this->rrule, $fromDate ?: now());
        
        return $this->save();
    }

    protected $casts = [
        'derniere_date' => 'date',
        'prochaine_date' => 'date',
    ];

    /**
     * Get the equipment that owns the maintenance plan.
     */
    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    /**
     * Get the technician that is assigned to the maintenance plan.
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    /**
     * Get the active work order for this maintenance plan.
     */
    public function activeWorkOrder()
    {
        return $this->hasOne(WorkOrder::class, 'maintenance_plan_id')
            ->whereIn('statut', ['en_cours', 'en_attente']);
    }

    /**
     * Get all work orders for this maintenance plan.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'maintenance_plan_id');
    }
}
