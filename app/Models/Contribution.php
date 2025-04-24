<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    //
    protected $fillable = [
        'employee_id',
        'contribution_type_id',
        'calculation_type',
        'value',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contributionType()
    {
        return $this->belongsTo(ContributionType::class);
    }
}
