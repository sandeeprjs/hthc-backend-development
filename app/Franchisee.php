<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Franchisee extends Model
{
    use SoftDeletes;

    
    protected $fillable = [
        'branch_id','code','gender','mobile_number','email','gender',
        'enterprise_name','add_line_1','city','state','country','pincode_id','contact_person_name',
        'phone_number','franchisee_type','current_bank_name',
        'branch_name', 'account_number', 'ifsc_code', 'avatar', 'doc_proof',
    ];
    //protected $appends = ['branch'];

    public function serviceablePins(){

        return $this->hasMany(ServiceablePin::class,'office_id','id');

    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function users(){
        return $this->hasMany(User::class);
    }
    public function pincode(){
        return $this->hasOne(Pincode::class,'id','pincode_id');
    }

}
