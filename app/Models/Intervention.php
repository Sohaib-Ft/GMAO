<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Intervention extends Model
{
    protected $fillable = [
        'work_order_id', 'technicien_id', 'description', 'duree', 'cout', 'date_debut', 'date_fin'
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

        protected static function booted()
    {
        static::updating(function ($intervention) {
            if ($intervention->isDirty('date_fin') && $intervention->date_debut) {
                $intervention->duree = Carbon::parse($intervention->date_fin)
                                            ->diffInMinutes(Carbon::parse($intervention->date_debut));
            }
        });
    }
}
