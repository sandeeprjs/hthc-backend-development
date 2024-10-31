<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'consg_type', 'price','max_delivery_time',
    ];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
