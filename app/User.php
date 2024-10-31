<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Import HasFactory
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use App\UserRole;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, HasApiTokens, HasFactory; // Use HasFactory

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 'mobile_number', 'office_type', 'office_id',
        'current_bank_name', 'branch_name', 'account_number', 'ifsc_code', 'avatar', 'doc_proof'
    ];

    /**
     * The attributes that should be hidden for arrays
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['roles'];

    public function user_role()
    {
        return $this->hasOne(UserRole::class);
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'office_id');
    }

    public function franchisee()
    {
        return $this->hasOne(Franchisee::class, 'id', 'office_id');
    }

    public function roles()
    {
        $userRoles = UserRole::where('user_id', $this->id)->get()->pluck('role_id');
        return Role::whereIn('id', $userRoles);
    }

    public function hasRole($role)
    {
        $roles = $this->roles()->get();

        foreach ($roles as $input) {
            if ($input->name == $role) {
                return true;
            }
        }

        return false;
    }

    public function getRolesAttribute()
    {
        if ($this->roles()->exists()) {
            return $this->roles()->get();
        }
    }

    public function isAdmin()
    {
        $roles = $this->roles()->get();
        foreach ($roles as $role) {
            if ($role->id == 1) {
                return true;
            }
        }
        return false;
    }

    public function modules()
    {
        $roles =  $this->roles;
        foreach ($roles as $role) {
            echo '<pre>';
            print_r($role->permissions()->with('modules')->get());
            echo '</pre>';
            exit();
        }

        $modules = array();
        foreach ($roles as $role) {
            $modules[] = $role->modules()->pluck('name');
        }
        return $modules;
    }

    public function office()
    {
        $officeType = $this->office_type;

        if ($officeType == 'FR') {
            return $this->belongsTo(Franchisee::class, 'office_id', 'id');
        } else {
            return $this->belongsTo(Branch::class, 'office_id', 'id');
        }
    }
}
