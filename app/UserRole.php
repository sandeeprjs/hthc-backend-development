<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

class UserRole extends Model
{
    protected $table = 'role_user';

    protected $fillable = [
        'user_id','role_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role(){
        return $this->belongsTo(Role::class); 
    }
}
