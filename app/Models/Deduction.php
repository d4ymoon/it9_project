<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    //
    protected $fillable = ['payroll_id','deduction_type_id','amount',];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class);
    }

}
