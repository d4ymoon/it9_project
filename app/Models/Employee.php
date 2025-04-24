<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $fillable = ['name', 'contact_number', 'email', 'position_id', 'hire_date', 'bank_acct', 'status'];

    public function payroll()
    {
    return $this->hasOne(Payroll::class);
    }

    public function position()
    {
    return $this->belongsTo(Position::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function calculateDeductionsForPeriod($start, $end)
    {
        return $this->payrolls()
            ->whereBetween('pay_period', [$start, $end])
            ->with('deductions')
            ->get()
            ->sum(function ($payroll) {
                return $payroll->deductions->sum('amount');
            });
    }

public function calculateDeductions()
{
    // Example: Assuming you have a `Deductions` model and a relationship on `Employee` (e.g., employee hasMany deductions)
    return $this->deductions()->sum('amount');
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
