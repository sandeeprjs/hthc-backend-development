<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

//    protected $appends = ['modules'];

    public function users() {
        return $this->belongsToMany(User::class)->using(UserRole::class);
    }

    public function permissions() {
        return $this->hasMany(Permission::class);
    }

    public function modules() {
        return $this->belongsToMany(Module::class, 'permissions', 'module_id', 'role_id');
    }

    public function hasSegment($segment) {
        if ($segment == 'franchisees') {
            $segment = 'partners';
        }

        $module = Module::where('name', '=', $segment)->first();

        if ($module) {
            return $module;
        }

        return false;
    }

    public function hasDeletePermission($moduleId) {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return true;
        }
        $permission = $this->permissions()->where('module_id', $moduleId)->first();
        return $permission->delete;
    }

}
