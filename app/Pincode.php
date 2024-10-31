<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Branch;

class Pincode extends Model
{
    use SoftDeletes;

   

    protected $fillable = [
        'pincode', 'area_name', 'zone', 'sub_zone', 'city', 'district', 'state', 'country', 'serviceable', 'status'
    ];

    
}
