<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hirarc extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_address',
        'assessor_name',
        'assessment_type',
        'assessment_date',
        'assessment_scope',
        'status',
        'user_id',
    ];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->user_id === $userId;
    }
}
