<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function index() {
        $user = Auth::user();
        $roles = $user->roles->pluck('id');
//        print_r($roles);

//        $modules = Module::whereNull('parent_id')->with(['children.permissions' => function ($q) use ($roles){
//            $count = $q->whereIn('role_id', $roles)->where('enabled', 1)->count();
//            return ($count > 0) ? true: false;
//        }])->get()->toArray();

        $modules = Module::whereNull('parent_id')->with(['children' => function ($children) use ($roles){
            $children->whereHas('permissions', function ($q) use ($roles){
                $q->whereIn('role_id', $roles)->where('enabled', 1);
            });
        }])->get()->toArray();

        $enabledModules = collect($modules)->filter(function($module){
            return $module['is_enabled'] == true;
        })->values();

//        return view('components.nav', compact('enabledModules'));
        return response()->json([
            'modules' => $enabledModules,
        ],200);
    }
}
