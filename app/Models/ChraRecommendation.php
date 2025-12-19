<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChraRecommendation extends Model
{
    protected $fillable = [
        'chra_id',
        'category',
        'recommendation',
        'action_priority',
    ];
}
