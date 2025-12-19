<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChraDeleteRequest extends Model
{
    protected $fillable = [
        'chra_id',
        'requested_by',
        'reason',
        'status',
    ];

    public function chra()
    {
        return $this->belongsTo(Chra::class);
    }
}
