<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'from_weight_kgs', 'to_weight_kgs', 'price' , 'addl_weight', 'addl_price', 'consg_type', 'remarks',
    ];
}
