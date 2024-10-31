<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Pincode;

class Customer extends Model
{
    use SoftDeletes;
      
    protected $fillable = [
        'code', 'customer_name', 'company_name','add_line_1', 'add_line_2', 'city', 'state', 'country',
        'pincode_id', 'email', 'email_verified_at', 'email_verification_code', 'mobile_number', 'mobile_verification_code',
        'mobile_verified_at','add_line_2','district', 'subscription_id', 'active', 'remarks',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    public function pincode()
    {
         return $this->belongsTo(Pincode::class);
    }

    public function customer_office()
    {
        $user = auth()->user();
        return $this->belongsToMany(CustomerOffice::class)
            ->where('office_type',' = ', $user->office_type)
            ->where('office_id ','=', $user->office_id);  
    }
   
}
