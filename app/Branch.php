<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ServiceablePin;
use App\Pincode;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_type','code','mobile_number','email','gender','phone_number',
        'branch_name','add_line_1','city','state','country','pincode_id','incharge_name'
        
    ];
    public function serviceablePins(){
       
        return $this->hasMany(ServiceablePin::class,'office_id','id');
   
    }

    public function franchisees(){
        return $this->hasMany(Franchisee::class);
    }

    public function pincode()
    {
        return $this->hasOne(Pincode::class,'pincode_id');
    }

    

}