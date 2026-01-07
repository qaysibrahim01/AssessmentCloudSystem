<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraExposure extends Model
{
    protected $fillable = [
        'nra_id',
        'nra_work_unit_id',
        'nra_chemical_id',
        'exposure_route',
        'task',
        'exposure_frequency',
        'exposure_duration',
        'existing_control',
        'control_adequacy',
        'exposure_rating',
    ];

    public function nra()
    {
        return $this->belongsTo(Nra::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(NraWorkUnit::class, 'nra_work_unit_id');
    }

    public function chemical()
    {
        return $this->belongsTo(NraChemical::class, 'nra_chemical_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(NraRiskEvaluation::class);
    }
}
