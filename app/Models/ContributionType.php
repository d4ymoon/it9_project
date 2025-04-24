<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributionType extends Model
{
    //
    protected $fillable = ['name'];

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }
}
