<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraWorkUnit extends Model
{
    protected $fillable = [
        'nra_id',
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

    public function nra()
    {
        return $this->belongsTo(Nra::class);
    }

    public function exposures()
    {
        return $this->hasMany(NraExposure::class);
    }
}
