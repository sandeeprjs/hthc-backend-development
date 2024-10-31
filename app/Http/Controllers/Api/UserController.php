<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function login(Request $request){

        $this->validate($request, [
                        'username' => 'required',
                        'password' => 'required'
        ]);
        $user = User::where('email', '=', $request->input('username'))
                    ->orWhere('username', '=', $request->input('username'))
                    ->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid Username'
            ],401);
        }
        // if user exist check for password
        if (!Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 0,
                'message' => 'Password doesn\'t match'
            ],401);
        }
        $token = $user->createToken('access_token')->accessToken;
        $office = $this->getOfficeDetails($user->office_id,$user->office_type);
        if(!$office){
            return response()->json([
                'status' => 0,
                'message' => 'Office Not Found'
            ],403);
        }
        $permission = [];
        if($user->roles){
            foreach($user->roles as $role){
                $permission = Permission::where('role_id', $role->id)->get();
                foreach($permission as $key => $perm){
                    $permission[$key]['moudle_name'] = $perm->modules->name;
                }
        
            }
        }
     
        return response()->json([
            'status' => 1,
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => $user,
            'office' => $office,
            'permission' => $permission
        ],200);
    }

    public function getOfficeDetails($office_id, $office_type){

            if($office_type == 'BR' || $office_type == 'HO'){
                $office = Branch::find($office_id);
            }
            if($office_type == 'FR'){
                $office = Franchisee::find($office_id);
            }
            if($office){
                return $office;
            }

            return false;
    }

    public function getEmpPhoto(Request $request){
           $user_id = $request->input('userid');
           $employee = User::find($user_id);
           $avatarLink = asset('storage/uploads/employees/photo/unknown_user.png');
           if($employee->avatar){
                if(is_file(public_path('storage/uploads/employees/photo/'.$employee->avatar))){
                   $avatarLink = asset('storage/uploads/employees/photo/'.$employee->avatar);
                }   
           }
           return response()->json([
            'status' => 1,
            'avatar' => $avatarLink
        ]);

    }
}
