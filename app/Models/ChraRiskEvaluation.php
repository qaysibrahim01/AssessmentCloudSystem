<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChraRiskEvaluation extends Model
{
    protected $fillable = [
        'chra_exposure_id',
        'exposure_rating',
        'hazard_rating',
        'risk_score',
        'risk_level',
        'action_priority',
    ];

    public function exposure()
    {
        return $this->belongsTo(ChraExposure::class, 'chra_exposure_id');
    }
}
