<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    //
    protected $fillable = [
        'employee_id', 'basic_pay', 'overtime_pay', 'total_deductions', 'taxable_income', 'tax', 'net_salary', 'pay_period',
    ];

    // Define the relationship with Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function recalculateTotalDeductions() {
        $this->total_deductions = $this->deductions()->sum('amount');

        // Calculate taxable income
        $taxable_income = ($this->basic_pay + $this->overtime_pay) - $this->total_deductions;
        $this->tax = $this->calculateTax($taxable_income);

        // Recalculate net salary
        $this->net_salary = ($this->basic_pay + $this->overtime_pay) - $this->total_deductions - $this->tax;

        $this->save();
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



}
