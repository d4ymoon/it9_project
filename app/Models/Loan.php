<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    protected $fillable = [
        'employee_id',
        'loan_type',
        'loan_amount',
        'deduction_percentage',
        'start_date',
        'remaining_balance',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'loan_amount' => 'decimal:2',
        'deduction_percentage' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
