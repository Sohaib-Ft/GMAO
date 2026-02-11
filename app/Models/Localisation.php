<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    protected $guarded = ['id'];

    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }
}
