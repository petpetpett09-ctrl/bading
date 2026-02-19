<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_number',
        'vendor',
        'bill_date',
        'due_date',
        'total_amount',
        'status', // draft, received, approved, paid, overdue
        'notes',
    ];

    protected $casts = [
        'bill_date' => 'datetime',
        'due_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['approved', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('bill_date', [$startDate, $endDate]);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('due_date', 'desc');
    }
}
