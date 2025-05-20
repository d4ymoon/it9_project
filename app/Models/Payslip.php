<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\ContributionCalculationService;

class Payslip extends Model
{
    protected $fillable = [
        'employee_id',
        'pay_period',
        'period_type',
        'hours_worked',
        'overtime_hours',
        'basic_pay',
        'overtime_pay',
        'loan_deductions',
        'total_deductions',
        'tax',
        'net_salary',
        'payment_status'
    ];

    protected $appends = ['contributions'];

    protected $with = ['employee.contributions.contributionType'];

    protected $casts = [
        'pay_date' => 'date',
        'basic_pay' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'holiday_pay' => 'decimal:2',
        'night_differential' => 'decimal:2',
        'gross_pay' => 'decimal:2',
        'sss_deduction' => 'decimal:2',
        'philhealth_deduction' => 'decimal:2',
        'pagibig_deduction' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'loan_deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'loan_details' => 'array'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getContributionsAttribute()
    {
        $contributionService = new ContributionCalculationService();
        $contributions = [];
        $totalContributions = 0;

        if ($this->employee && $this->employee->contributions) {
            foreach ($this->employee->contributions as $contribution) {
                if ($contribution->calculation_type === 'salary_based' && $contribution->contributionType) {
                    $amount = $contributionService->calculateContribution($this->basic_pay, $contribution);
                    $contributions[$contribution->contributionType->name] = $amount;
                    $totalContributions += $amount;
                }
            }
        }

        return [
            'details' => $contributions,
            'total' => $totalContributions
        ];
    }

    public function calculateTax($taxable_income) {
        if ($taxable_income <= 10417) {
            return 0;
        } elseif ($taxable_income <= 16666) {
            return 0 + 0.15 * ($taxable_income - 10417);
        } elseif ($taxable_income <= 33332) {
            return 1250 + 0.20 * ($taxable_income - 16667);
        } elseif ($taxable_income <= 83332) {
            return 5416.67 + 0.25 * ($taxable_income - 33333);
        } elseif ($taxable_income <= 333332) {
            return 20416.67 + 0.30 * ($taxable_income - 83333);
        } else {
            return 100416.67 + 0.35 * ($taxable_income - 333333);
        }
    }

    protected static function boot()
    {
        parent::boot();

        // Remove the automatic calculation of total_deductions
        // The calculation should only happen in the controller
    }
}
