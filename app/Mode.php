<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mode extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'code', 'name', 'type', 'description',
    ];

    public function Dispatch()
    {
        return $this->hasMany(Dispatch::class);
    }
}
