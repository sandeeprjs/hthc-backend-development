<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOffice extends Model
{
    //
    protected $table = 'customer_office'; 
    protected $fillable = ['customer_id','office_type','office_id'];

    public function customer(){
        return $this->belongsToMany(Customer::class);
    }
}
