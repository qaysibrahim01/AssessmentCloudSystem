<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirarcRiskEvaluation extends Model
{
    protected $fillable = [
        'hirarc_exposure_id',
        'exposure_rating',
        'hazard_rating',
        'risk_score',
        'risk_level',
        'action_priority',
    ];

    public function exposure()
    {
        return $this->belongsTo(HirarcExposure::class, 'hirarc_exposure_id');
    }
}
