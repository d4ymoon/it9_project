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
}
