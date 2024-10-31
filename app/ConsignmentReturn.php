<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsignmentReturn extends Model
{
    //
    //use Softdeletes;
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consg_number', 'reason_id', 'return_mode', 'user_id'       
    ];

    public function reason(){
        return $this->hasOne(Reason::class,'id','reason_id');
    }
}
