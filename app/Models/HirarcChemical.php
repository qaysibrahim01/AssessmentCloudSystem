<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirarcChemical extends Model
{
    protected $fillable = [
        'hirarc_id',
        'hirarc_work_unit_id',
        'chemical_name',
        'is_chth',
        'health_hazard',
        'h_code',
        'route_inhalation',
        'route_dermal',
        'route_ingestion',
        'hazard_rating',
    ];

    public function hirarc()
    {
        return $this->belongsTo(Hirarc::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(HirarcWorkUnit::class, 'hirarc_work_unit_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(HirarcRiskEvaluation::class);
    }

    public function exposures()
    {
        return $this->hasMany(HirarcExposure::class);
    }
}
