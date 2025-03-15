<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\User;
use App\Branch;
use App\Franchisee;
use App\Permission;
use App\Module;
use URL;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        // Cache the user lookup for frequently used usernames
        $usernameInput = $request->input('username');
        $userCacheKey = "user_by_username_or_email_{$usernameInput}";

        $user = Cache::remember($userCacheKey, 600, function () use ($usernameInput) {
            return User::where('email', $usernameInput)
                ->orWhere('username', $usernameInput)
                ->first();
        });

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Username'
            ], 401);
        }

        // Check password
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Password doesn\'t match'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('access_token')->accessToken;

        // Get office details with caching
        $officeType = $user->office_type;
        $officeId = $user->office_id;
        $officeCacheKey = "office_{$officeType}_{$officeId}";

        $office = Cache::remember($officeCacheKey, 3600, function () use ($officeId, $officeType) {
            return $this->getOfficeDetails($officeId, $officeType);
        });

        if (!$office) {
            return response()->json([
                'status' => 0,
                'message' => 'Office Not Found'
            ], 403);
        }

        // Get permissions with caching
        $permissionCacheKey = "user_permissions_{$user->id}";
        $permission = Cache::remember($permissionCacheKey, 3600, function () use ($user) {
            $permissions = [];

            if ($user->roles) {
                foreach ($user->roles as $role) {
                    $rolePermissions = Permission::with('modules')
                        ->where('role_id', $role->id)
                        ->get();

                    if ($rolePermissions->isNotEmpty()) {
                        $permissions = $rolePermissions->map(function ($perm) {
                            $perm['moudle_name'] = $perm->modules->name;
                            return $perm;
                        })->toArray();
                    }
                }
            }

            return $permissions;
        });

        return response()->json([
            'status' => 1,
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => $user,
            'office' => $office,
            'permission' => $permission
        ], 200);
    }

    public function getOfficeDetails($office_id, $office_type)
    {
        if ($office_type == 'BR' || $office_type == 'HO') {
            return Branch::find($office_id);
        }

        if ($office_type == 'FR') {
            return Franchisee::find($office_id);
        }

        return false;
    }

    public function getEmpPhoto(Request $request)
    {
        $user_id = $request->input('userid');
        $cacheKey = "employee_avatar_{$user_id}";

        $avatarLink = Cache::remember($cacheKey, 3600, function () use ($user_id) {
            $defaultAvatar = asset('storage/uploads/employees/photo/unknown_user.png');
            $employee = User::find($user_id);

            if (!$employee || !$employee->avatar) {
                return $defaultAvatar;
            }

            if (is_file(public_path('storage/uploads/employees/photo/' . $employee->avatar))) {
                return asset('storage/uploads/employees/photo/' . $employee->avatar);
            }

            return $defaultAvatar;
        });

        return response()->json([
            'status' => 1,
            'avatar' => $avatarLink
        ]);
    }
}
