<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirarcRecommendation extends Model
{
    protected $fillable = [
        'hirarc_id',
        'category',
        'recommendation',
        'action_priority',
    ];
}
