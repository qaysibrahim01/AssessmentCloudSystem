<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChraWorkUnit extends Model
{
    protected $fillable = [
        'chra_id',
        'name',
        'work_area',
        'male_count',
        'female_count',
        'main_task',
        'exposure_duration',
    ];

    protected $appends = ['total_workers'];

    public function getTotalWorkersAttribute()
    {
        return ($this->male_count ?? 0) + ($this->female_count ?? 0);
    }

    public function chra()
    {
        return $this->belongsTo(Chra::class);
    }

    public function exposures()
    {
        return $this->hasMany(ChraExposure::class);
    }
}
