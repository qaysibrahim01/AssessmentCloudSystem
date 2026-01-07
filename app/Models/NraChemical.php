<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraChemical extends Model
{
    protected $fillable = [
        'nra_id',
        'nra_work_unit_id',
        'chemical_name',
        'is_chth',
        'health_hazard',
        'h_code',
        'route_inhalation',
        'route_dermal',
        'route_ingestion',
        'hazard_rating',
    ];

    public function nra()
    {
        return $this->belongsTo(Nra::class);
    }

    public function workUnit()
    {
        return $this->belongsTo(NraWorkUnit::class, 'nra_work_unit_id');
    }

    public function riskEvaluation()
    {
        return $this->hasOne(NraRiskEvaluation::class);
    }

    public function exposures()
    {
        return $this->hasMany(NraExposure::class);
    }
}
