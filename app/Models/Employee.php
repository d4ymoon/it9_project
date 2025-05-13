<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $fillable = [
        'name',
        'contact_number',
        'email',
        'position_id',
        'shift_id',
        'hire_date',
        'bank_name',
        'bank_acct',
        'payment_method',
        'status',
        'user_id'
    ];

    public function payroll()
    {
        return $this->hasOne(Payroll::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateSemiTax($taxable_income) {
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

    public function calculateMonthlyTax(float $taxable_income): float
    {
        if ($taxable_income <= 20_833) {
            return 0.0;
        } elseif ($taxable_income <= 33_332) {
            return ($taxable_income - 20_833) * 0.15;
        } elseif ($taxable_income <= 66_666) {
            return 1_875.00 + ($taxable_income - 33_333) * 0.20;
        } elseif ($taxable_income <= 166_666) {
            return 8_541.80 + ($taxable_income - 66_667) * 0.25;
        } elseif ($taxable_income <= 666_666) {
            return 33_541.80 + ($taxable_income - 166_667) * 0.30;
        } else {
            return 183_541.80 + ($taxable_income - 666_667) * 0.35;
        }
    }
}
