<?php

namespace App\Models;

use App\Services\AssetCodeGenerator;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'date_installation' => 'date',
        'date_fin_garantie' => 'date',
        'tags' => 'array',
    ];

    /**
     * Boot method - génère automatiquement le code si non fourni
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipement) {
            if (empty($equipement->code)) {
                $generator = new AssetCodeGenerator();
                $equipement->code = $generator->generate($equipement);
            }
        });
    }

    /**
     * Relations
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'equipement_id');
    }

    public function maintenancePlans()
    {
        return $this->hasMany(MaintenancePlan::class, 'equipement_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function departementRelation()
    {
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function localisationRelation()
    {
        return $this->belongsTo(Localisation::class, 'localisation_id');
    }

    /**
     * Backwards-compatible relation name used across views/controllers.
     */
    public function localisation()
    {
        return $this->belongsTo(Localisation::class, 'localisation_id');
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class, 'equipment_type_id');
    }

    public function serial()
    {
        return $this->hasOne(EquipementSerial::class);
    }

    public function years()
    {
        return $this->hasOne(EquipementYear::class);
    }

    /**
     * Accessors & Mutators
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->nom}";
    }

    /**
     * Site Accessor & Mutator
     */
    public function getSiteAttribute()
    {
        return $this->localisationRelation?->name;
    }

    public function setSiteAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['localisation_id'] = null;
            return;
        }

        if (is_numeric($value)) {
            $this->attributes['localisation_id'] = $value;
            return;
        }

        $localisation = Localisation::firstOrCreate(['name' => $value]);
        $this->attributes['localisation_id'] = $localisation->id;
    }

    /**
     * Departement Accessor & Mutator
     */
    public function getDepartementAttribute()
    {
        return $this->departementRelation?->name;
    }

    public function setDepartementAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['departement_id'] = null;
            return;
        }

        $dept = Departement::firstOrCreate(['name' => $value]);
        $this->attributes['departement_id'] = $dept->id;
    }

    /**
     * Numero Serie Accessor & Mutator
     */
    public function getNumeroSerieAttribute()
    {
        return $this->serial?->serial;
    }

    public function setNumeroSerieAttribute($value)
    {
        $this->serial()->updateOrCreate([], ['serial' => $value]);
    }

    /**
     * Years Accessor & Mutator
     */
    public function getAnneeFabricationAttribute()
    {
        return $this->years?->annee_fabrication;
    }

    public function setAnneeFabricationAttribute($value)
    {
        $this->years()->updateOrCreate([], ['annee_fabrication' => $value]);
    }

    public function getAnneeAcquisitionAttribute()
    {
        return $this->years?->annee_acquisition;
    }

    public function setAnneeAcquisitionAttribute($value)
    {
        $this->years()->updateOrCreate([], ['annee_acquisition' => $value]);
    }

    /**
     * Equipment Type Accessor & Mutator
     */
    public function getTypeEquipementAttribute()
    {
        return $this->equipmentType?->name;
    }

    public function setTypeEquipementAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['equipment_type_id'] = null;
            return;
        }

        if (is_numeric($value)) {
            $this->attributes['equipment_type_id'] = $value;
            return;
        }

        $type = EquipmentType::firstOrCreate(['name' => $value]);
        $this->attributes['equipment_type_id'] = $type->id;
    }

    /**
     * Scopes
     */
    public function scopeByDepartement($query, string $departement)
    {
        return $query->whereHas('departementRelation', function($q) use ($departement) {
            $q->where('name', $departement);
        });
    }

    public function scopeBySite($query, string $site)
    {
        return $query->whereHas('localisationRelation', function($q) use ($site) {
            $q->where('name', $site);
        });
    }

    public function scopeByCategorie($query, string $type)
    {
        return $query->whereHas('equipmentType', function($q) use ($type) {
            $q->where('name', $type);
        });
    }

}