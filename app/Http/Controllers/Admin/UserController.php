<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Branch;
use App\Franchisee;
use App\Permission;
use App\Module;

class UserController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth', 'role'])->except(
            'login'
        );
    }

    

    public function test()
    {
        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
