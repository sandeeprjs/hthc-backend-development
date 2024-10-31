<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $appends = ['origin_pincode', 'subs_name', 'branch_details'];

    protected $fillable = [
        'consg_number',
        'consg_type',
        'subscription_id',
        'customer_id',
        'customer_name',
        'gender',
        'mobile_number',
        'phone_number',
        'email',
        'add_line_1',
        'add_line_2',
        'landmark',
        'city',
        'state',
        'pincode_id',
        'weight',
        'final_weight',
        'booking_status',
        'length',
        'breadth',
        'height',
        'final_height',
        'final_breadth',
        'final_length',
        'booked_amount',
        'booking_date',
        'final_amount',
        'amount_due',
        'payment_mode',
        'payment_id',
        'insured',
        'insured_by',
        'declared_consg_value',
        'insurance_amt',
        'branch_id',
        'franchisee_id',
        'booking_user_id',
        'booking_modified',
        'status',
        'sms_to_receiver',
        'remarks',
        'mode_id',
        'origin_office_id',
        'origin_office_type'
    ];

    public function delivery() {
        return $this->hasOne(Delivery::class);
    }

    public function subscription() {
        return $this->belongsTo(Subscription::class);
    }

    public function getSubsNameAttribute() {
        $subscription = $this->subscription()->select(['name'])->first();
        if ($subscription) {
            return $subscription->name;
        } else {
            return null;
        }

    }

    public function pincode() {
        return $this->belongsTo(Pincode::class);
    }

    public function getOriginPincodeAttribute() {
        $pincode = $this->pincode()->select(['pincode'])->first();
        return $pincode->pincode ?? null;
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'dest_branch_id');
    }

    public function bookingBranch(){
        return $this->belongsTo(Branch::class, 'origin_office_id')->where('branch_type','HO')->orWhere('branch_type','BR');

    }

    public function getBookingBranchAttribute(){

        $bookingBranch =  $this->bookingBranch()->select(['code'])->first();

        if($bookingBranch){
        return $bookingBranch['code'];
        }
        return null;

   }
   public function getBranchDetailsAttribute(){

    $bookingBranch =  $this->bookingBranch()->select(['code','branch_name'])->first();

    if($bookingBranch){
    return $bookingBranch;
    }
    return null;

}

    public function bookingFranchisee(){
        return $this->belongsTo(Franchisee::class, 'origin_office_id');

    }

    public function getBookingFranchiseeAttribute(){

         $bookingFranchisee =  $this->bookingFranchisee()->select(['code'])->first();
         if($bookingFranchisee){
             return $bookingFranchisee->code;
         }
         return null;

    }

    public function office() {
        $officeType = $this->office_type;

        if ($officeType == 'FR') {
            return $this->belongsTo(Franchisee::class, 'origin_office_id');
        } else {
            return $this->belongsTo(Branch::class, 'origin_office_id')->where('branch_type','HO')->orWhere('branch_type','BR');
        }
    }

    public function user(){
        return $this->hasOne(User::class,'id','booking_user_id');
    }

    public function returnReason(){
        return $this->hasOne(ConsignmentReturn::class,'consg_number','consg_number');
    }
    
    public function totalWeightDox() {
        return $this->hasMany(Booking::class)->selectRaw(['sum(weight) as dox_weight','id'])->where('consg_type',$this->consg_type);
       // return $this->hasMany(Booking::class,'weight')->where('consg_type', 'dox')->sum('weight');
    }

}
