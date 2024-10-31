<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Branch;
use App\Franchisee;
use App\Booking;

class Manifest extends Model
{
    //
    use SoftDeletes;

    protected $appends = ['sender_branch', 'receiver_branch', 'sender_franchisee', 'receiver_franchisee'];


    protected $fillable = [
        'manifest_type',
        'manifest_number',
        'origin_branch_id',
        'origin_pincode_id',
        'dest_pincode_id',
        'dest_branch_id',
        'sender_id',
        'sender_type',
        'receiver_id',
        'receiver_type',
        'consg_number_id',
        'last_mile_delivery',
        'delivery_user_id',
        'status',
        'remarks',
        'user_id',
        'office_id',
        'office_type'
    ];

    public function branchSender(){
        return $this->hasOne(Branch::class,'id','sender_id');
    }
    public function branchReceiver(){
        return $this->hasOne(Branch::class,'id','receiver_id');
    }

    public function franchiseeSender(){
        return $this->hasOne(Franchisee::class,'id','sender_id');
    }
    public function franchiseeReceiver(){
        return $this->hasOne(Franchisee::class,'id','receiver_id');
    }

    public function getSenderBranchAttribute() {
       return $sender = $this->branchSender()->select(['code','branch_name'])->first();
        
    }

    public function getReceiverBranchAttribute() {
       return $receiver = $this->branchReceiver()->select(['code','branch_name'])->first();
        
    }

    public function getSenderFranchiseeAttribute() {
        return $receiver = $this->franchiseeSender()->select(['code','enterprise_name'])->first();
        
    }

    public function getReceiverFranchiseeAttribute() {
       return $receiver = $this->franchiseeReceiver()->select(['code','enterprise_name'])->first();
       
    }

   
    public function booking(){
        return $this->hasOne(Booking::class,'consg_number','manifest_number');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    // public function pincodeOrigin(){

    //     //return $this->belongsTo(Pincode::class, 'id', 'origin_pincode_id'); 
    //     return $this->hasOne(Pincode::class, 'origin_pincode_id'); 
    // }
    // public function pincodeDestination(){

    //     // return $this->belongsTo(Pincode::class,'id', 'dest_pincode_id'); 
    //     return $this->hasOne(Pincode::class, 'id', 'dest_pincode_id'); 
    // }

    

}