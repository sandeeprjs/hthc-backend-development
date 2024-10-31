<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Consignment extends Model
{
    use SoftDeletes;
    protected $appends = ['UsedConsignment'];
    public function office() {
        $officeType = $this->office_type;

        if ($officeType == 'FR') {
            return $this->belongsTo(Franchisee::class, 'office_id', 'id');
        } else {
            return $this->belongsTo(Branch::class, 'office_id', 'id');
        }
    }

    public function hasDeletePermission($moduleId) {
        $user = Auth::user();
        if ($user->id == 1) {
            return true;
        }
        $permission = $this->permissions()->where('module_id', $moduleId)->first();
        return $permission->delete;
    }

    public function getUsedConsignmentAttribute()
    {
      return Consignment::join( 'bookings' , "consignments.consg_number", "=", "bookings.consg_number")
                   ->where('consignments.batch_id',$this->batch_id)->count();
    }

  
}

