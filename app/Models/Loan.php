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
        'interest_rate', 
        'deduction_percentage',
        'start_date',
        'remaining_balance',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'loan_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',  
        'deduction_percentage' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate the monthly interest based on the remaining balance and interest rate
     */
    public function calculateMonthlyInterest(): float
    {
        return round(($this->remaining_balance * ($this->interest_rate / 100)) / 12, 2);
    }

    /**
     * Calculate the monthly principal payment based on the employee's salary
     */
    public function calculateMonthlyDeduction(float $monthlySalary): float
    {
        $deduction = ($monthlySalary * $this->deduction_percentage) / 100;
        return min($deduction, $this->remaining_balance);
    }

    /**
     * Process the monthly payment and update the loan balance
     */
    public function processMonthlyPayment(float $monthlySalary): void
    {
        $monthlyDeduction = $this->calculateMonthlyDeduction($monthlySalary);
        $monthlyInterest = $this->calculateMonthlyInterest();
        
        $this->remaining_balance = max(0, $this->remaining_balance - $monthlyDeduction);
        
        if ($this->remaining_balance == 0) {
            $this->status = 'paid';
        }
        
        $this->save();
    }
}
