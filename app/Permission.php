<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'enabled',
        'create',
        'read',
        'update',
        'delete'
    ];

   //protected $appends = ['module'];

    public function modules() {
            return $this->belongsTo(Module::class, 'module_id');
    }

//    public function getModuleAttribute() {
//        return $this->modules()->first();
//    }
}
