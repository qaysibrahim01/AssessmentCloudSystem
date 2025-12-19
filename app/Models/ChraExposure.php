<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChraExposure extends Model
{
    protected $fillable = [
        'chra_id',
        'chra_work_unit_id',
        'chra_chemical_id',
        'exposure_route',
        'task',
        'exposure_frequency',
        'exposure_duration',
        'existing_control',
        'control_adequacy',
        'exposure_rating',
    ];

    public function chra()
    {
        return $this->belongsTo(Chra::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(ChraWorkUnit::class, 'chra_work_unit_id');
    }

    public function chemical()
    {
        return $this->belongsTo(ChraChemical::class, 'chra_chemical_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(ChraRiskEvaluation::class);
    }
}
