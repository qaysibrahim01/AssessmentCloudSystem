<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NraRecommendation extends Model
{
    protected $fillable = [
        'nra_id',
        'category',
        'recommendation',
        'action_priority',
    ];
}
