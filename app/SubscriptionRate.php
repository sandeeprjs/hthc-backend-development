<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionRate extends Model
{
    protected $fillable = [
        'subscription_id', 'from_weight_kgs', 'to_weight_kgs','charges',
    ];
}
