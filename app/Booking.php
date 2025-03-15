<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    // Add with property to eagerly load these relationships by default
    protected $with = ['customer', 'pincode', 'subscription', 'user'];

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
        return $this->subscription->name ?? null;
    }

    public function pincode() {
        return $this->belongsTo(Pincode::class);
    }

    public function getOriginPincodeAttribute() {
        return $this->pincode->pincode ?? null;
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class, 'dest_branch_id');
    }

    public function bookingBranch() {
        return $this->belongsTo(Branch::class, 'origin_office_id')
            ->where(function($query) {
                $query->where('branch_type', 'HO')
                    ->orWhere('branch_type', 'BR');
            });
    }

    public function getBookingBranchAttribute() {
        return $this->bookingBranch->code ?? null;
    }

    public function getBranchDetailsAttribute() {
        return $this->bookingBranch ?? null;
    }

    public function bookingFranchisee() {
        return $this->belongsTo(Franchisee::class, 'origin_office_id');
    }

    public function getBookingFranchiseeAttribute() {
        return $this->bookingFranchisee->code ?? null;
    }

    public function office() {
        if ($this->origin_office_type == 'FR') {
            return $this->belongsTo(Franchisee::class, 'origin_office_id');
        } else {
            return $this->belongsTo(Branch::class, 'origin_office_id')
                ->where(function($query) {
                    $query->where('branch_type', 'HO')
                        ->orWhere('branch_type', 'BR');
                });
        }
    }

    public function user() {
        return $this->belongsTo(User::class, 'booking_user_id');
    }

    public function returnReason() {
        return $this->hasOne(ConsignmentReturn::class, 'consg_number', 'consg_number');
    }

    public function totalWeightDox() {
        return $this->hasMany(Booking::class)
            ->selectRaw('SUM(weight) as dox_weight, id')
            ->where('consg_type', $this->consg_type);
    }
}
