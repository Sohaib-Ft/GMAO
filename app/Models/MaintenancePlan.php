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
        'interval_jours',
        'derniere_date',
        'prochaine_date',
        'statut',
        'technicien_id',
    ];

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
}
