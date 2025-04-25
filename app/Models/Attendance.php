<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $fillable = ['employee_id','date','morning_time_in','morning_time_out','afternoon_time_in','afternoon_time_out','status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
