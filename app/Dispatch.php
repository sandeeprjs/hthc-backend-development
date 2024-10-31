<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatch extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'dest_office_id', 'vehicle_number', 'mode_id','consg_number', 'status',
    ];

    public function mode()
    {
        return $this->belongsTo(Mode::class);
    }

}
