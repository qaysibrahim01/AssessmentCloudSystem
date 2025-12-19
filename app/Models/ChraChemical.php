<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChraChemical extends Model
{
    protected $fillable = [
        'chra_id',
        'chra_work_unit_id',
        'chemical_name',
        'is_chth',
        'health_hazard',
        'h_code',
        'route_inhalation',
        'route_dermal',
        'route_ingestion',
        'hazard_rating',
    ];

    public function chra()
    {
        return $this->belongsTo(Chra::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(ChraWorkUnit::class, 'chra_work_unit_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(ChraRiskEvaluation::class);
    }

    public function exposures()
    {
        return $this->hasMany(ChraExposure::class);
    }


}
