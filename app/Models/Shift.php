<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    //
    protected $fillable = ['name','start_time','break_start_time','break_end_time','end_time','description','is_active'];

}
