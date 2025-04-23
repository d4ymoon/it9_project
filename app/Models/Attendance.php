<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $fillable = ['employee_id', 'time_in', 'time_out', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
