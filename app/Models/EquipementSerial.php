<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipementSerial extends Model
{
    protected $guarded = ['id'];

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }
}
