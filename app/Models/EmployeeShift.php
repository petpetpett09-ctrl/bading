<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeShift extends Model
{
    protected $fillable = [
        'user_id',
        'dept_code',
        'shift_type',
        'effective_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
