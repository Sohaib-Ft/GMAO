<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WorkOrder extends Model
{
    protected $table = 'work_order';
    protected $fillable = [
        'employe_id', 'technicien_id', 'equipement_id',
        'titre', 'description', 'priorite', 'statut',
        'date_creation', 'date_debut', 'date_fin', 'duree'
    ];

    // Relations
    public function employe()
    {
        return $this->belongsTo(User::class, 'employe_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }

    // Observer pour gérer dates et durée
    protected static function booted()
    {
        static::updating(function ($workOrder) {
            if ($workOrder->isDirty('statut')) {
                if ($workOrder->statut === 'en_cours' && !$workOrder->date_debut) {
                    $workOrder->date_debut = Carbon::now();
                }

                if ($workOrder->statut === 'terminee' && !$workOrder->date_fin) {
                    $workOrder->date_fin = Carbon::now();
                    if ($workOrder->date_debut) {
                        $workOrder->duree = Carbon::parse($workOrder->date_fin)
                                                ->diffInMinutes(Carbon::parse($workOrder->date_debut));
                    }
                }
            }
        });
    }
}

