<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirarcExposure extends Model
{
    protected $fillable = [
        'hirarc_id',
        'hirarc_work_unit_id',
        'hirarc_chemical_id',
        'exposure_route',
        'task',
        'exposure_frequency',
        'exposure_duration',
        'existing_control',
        'control_adequacy',
        'exposure_rating',
    ];

    public function hirarc()
    {
        return $this->belongsTo(Hirarc::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(HirarcWorkUnit::class, 'hirarc_work_unit_id');
    }

    public function chemical()
    {
        return $this->belongsTo(HirarcChemical::class, 'hirarc_chemical_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(HirarcRiskEvaluation::class);
    }
}
