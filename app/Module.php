<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Module extends Model
{
    use SoftDeletes;

    protected $appends = ['is_enabled'];

    public function permissions() {
        return $this->hasMany(Permission::class, 'module_id');
    }

    public function children() {
        return $this->hasMany(Module::class,'parent_id');
    }

//    public function getChildrenAttribute(){
//        return $this->children()->with(['permissions' => function ($q) {
//            $q->where('role_id', $role->id);
//        }])->get();
//    }

    public function isEnabled() {
        $user = Auth::user();
        if($user) {
            $roles = $user->roles()->select('roles.id')->pluck('id');
            $count = $this->permissions()->whereIn('role_id',$roles)->where('enabled','=',true)->count();
            return ($count > 0) ? true : false;
        } else {
            return false;
        }
    }

    public function getIsEnabledAttribute(){
        return $this->isEnabled();
    }

    public function userPermissions(){
        $user = Auth::user();
        $roles = $user->roles()->select('roles.id')->pluck('id');
        $createCount = $this->permissions()->whereIn('role_id',$roles)->where('create','=',true)->count();
        $readCount = $this->permissions()->whereIn('role_id',$roles)->where('read','=',true)->count();
        $updateCount = $this->permissions()->whereIn('role_id',$roles)->where('update','=',true)->count();
        $deleteCount = $this->permissions()->whereIn('role_id',$roles)->where('delete','=',true)->count();
        return [
            'create' => ($createCount > 0) ? true : false,
            'read' => ($readCount > 0) ? true : false,
            'update' => ($updateCount > 0) ? true : false,
            'delete' => ($deleteCount > 0) ? true : false,
        ];
    }

    public function getPermissionAttribute() {
        return $this->permissions()->get();
    }
}
