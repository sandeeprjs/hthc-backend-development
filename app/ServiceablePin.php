<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceablePin extends Model
{
    //
    protected $fillable = [
        'office_type','office_id','pincode_id'
    ];

   public function branches(){
      
       return $this->belongsTo(Branch::class,'id','office_id');
   }

   public function franchisees(){
   
       return $this->belongsTo(Franchisee::class,'id','office_id');
   }

   public function pincodes(){

     return $this->hasOne(Pincode::class,'id','pincode_id');
   }

}
