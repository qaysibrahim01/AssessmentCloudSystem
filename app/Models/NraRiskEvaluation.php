<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraRiskEvaluation extends Model
{
    protected $fillable = [
        'nra_exposure_id',
        'exposure_rating',
        'hazard_rating',
        'risk_score',
        'risk_level',
        'action_priority',
    ];

    public function exposure()
    {
        return $this->belongsTo(NraExposure::class, 'nra_exposure_id');
    }
}
