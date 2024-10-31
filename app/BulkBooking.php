<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulkBooking extends Model
{
    protected $guarded = [];

    public function pincode() {
        return $this->belongsTo(Pincode::class, 'receiver_pincode_id');
    }
}
