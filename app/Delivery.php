<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delivery extends Model
{
    use SoftDeletes;

    protected $appends = ['dest_pincode'];

    protected $fillable = [
        'booking_id',
        'receiver_name',
        'add_line_1',
        'add_line_2',
        'pincode_id',
        'city',
        'district',
        'state',
        'country_id',
        'mobile_number',
        'phone_number',
        'email',
        'delivery_status',
        'delivery_user_id'
    ];

    public function booking() {
        return $this->belongsTo(Booking::class);
    }

    public function pincode() {
        return $this->hasOne(Pincode::class, 'id', 'pincode_id');
    }

    public function getDestPincodeAttribute() {
        $pincode = $this->pincode()->select(['pincode'])->first();
        if ($pincode) {
            return $pincode->pincode;
        } else {
            return false;
        }
    }

    public function files() {
		    return $this->morphToMany(File::class, 'fileable');
    }

    public function receiverImageUrl() {
       return $this->files()->where('files.type', '=', 'receiver_photo')->select('url');
    }

    public function receiverSignUrl() {
        return $this->files()->where('files.type', '=', 'receiver_sign')->select('url');
    }
    public function receiverVoiceUrl() {
        return $this->files()->where('files.type', '=', 'receiver_voice')->select('url');
    }

    public function receiverAcknowledgement(){
         return $this->files()->where('fileable_type', '=', 'App\Delivery')->select('url');
    }

    public function deliveryBranch(){
        return $this->hasOne(User::class,'id','delivery_user_id');
    }

    // public function user(){
    //     return $this->hasOne(User::class,'id','delivery_user_id');
    // }
    public function user() {
        return $this->belongsTo(User::class,'delivery_user_id','id');
    }
   


}
