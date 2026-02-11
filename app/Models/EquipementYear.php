<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipementYear extends Model
{
    protected $guarded = ['id'];

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }
}
