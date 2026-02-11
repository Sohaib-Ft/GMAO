<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    protected $guarded = ['id'];

    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'equipment_type_id');
    }
}
