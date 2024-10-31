<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Rules\MatchOldPassword;
use App\User;
use App\UserRole;
use App\Franchisee;
use App\Branch;
use App\Role;
use Session;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      return $this->middleware(['auth', 'role'])->except(['findBranch']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'filter_by' => 'nullable',
            'filter_val' => 'nullable'
        ]);

        $user = auth()->user();
        
        $filter_by = $request->input('filter_by');
        $filter_val = $request->input('filter_val');
        $employees = User::where('users.user_type', '<>', 'admin')
       
        ->orWhereNull('user_type')->with('user_role')
        
        ->when($filter_by, function ($q) use ($filter_by, $filter_val, $user){

                            if($filter_by == 'BR'){
                                $office = Branch::where('code','=', $filter_val)->first();
                                return $q->where('users.office_type','=', "$filter_by")
                                         ->where('users.office_id','=',($office) ? $office->id : 0);
                            }
                            if($filter_by == "FR"){
                                $office = Franchisee::where('code','LIKE', "%$filter_val%")->first();
                                return $q->where('users.office_type','=', "$filter_by")
                                         ->where('users.office_id','=',($office) ? $office->id : 0);
                            }
                            if($filter_by == "EMPC"){
                                return $q->where('users.username','LIKE', "%$filter_val%");
                            }
                            if($filter_by == "EMPN"){
                                return $q->where(DB::raw("CONCAT(`first_name`, ' ', `last_name`)"), 'LIKE', '%' . $filter_val . '%');
                            }
                            if($filter_by == "MBL"){
                                return $q->where('users.mobile_number','=', "$filter_val");
                            }
                           
                    })
                    ->when(!$user->isAdmin(), function($q) use ($user){
                        if(!$user->isAdmin()){
                            return $q->where('users.office_type', '=', $user->office_type)
                            ->where('users.office_id', '=', $user->office_id);
                        }
                    })


                    ->leftJoin('branches', 'branches.id', '=', 'users.office_id')
                    ->leftJoin('franchisees', 'franchisees.id', '=', 'users.office_id')
                   
                    ->select('branches.code as br_code','franchisees.code as fr_code','users.*')
                   
                    ->orderBy('updated_at', 'desc')

                    ->paginate('10');



        return view('employees.index',compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $type = $request->input('office_type');
        $branches = Branch::get();
        if($type == 'FR'){
            $branches = Franchisee::get();
        }

        $user = User::withTrashed()->latest()->first();
        $empCode = 'EMP00'.($user->id+1);
        $roles = Role::get();

        return view('employees.create',compact('branches','empCode', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = $request->all();
        //$data['password'] = Hash::make($data['password']);

        $this->validate(
            $request, [
                 'office_type' => 'required',
                 'office_id' => 'required',
                 'role_id' => 'required',
                 'first_name' => 'required|regex:/^[a-zA-Z ]+$/',
                 'last_name' => 'required|regex:/^[a-zA-Z ]+$/',
                 'username' => 'required',
                 'password' => ['required', 'string', 'min:8', 'confirmed'],
                 'email' => 'email|max:255|unique:users,email',
                 'mobile_number' => 'required|digits:10|numeric|unique:users',
                 'current_bank_name' => '',
                 'branch_name' => '',
                 'account_number' => ' ',
                 'ifsc_code' => '',
                 'avatar' => 'image|mimes:jpeg,png,jpg|max:4028',
                 'doc_proof' => 'image|mimes:jpeg,png,jpg|max:4028',
                ]
        );
        $avatarimage = null;
        if (request()->hasFile('avatar')) {
            $profile = request()->file('avatar');
            $avatarimage = md5($profile->getClientOriginalName() . time()) . "." . $profile->getClientOriginalExtension();
            $profile->move('./storage/uploads/employees/photo', $avatarimage);
        }
        $doc_proof = null;
        if (request()->hasFile('doc_proof')) {
            $doc = request()->file('doc_proof');
            $doc_proof = md5($doc->getClientOriginalName() . time()) . "." . $doc->getClientOriginalExtension();
            $doc->move('./storage/uploads/employees/idproof', $doc_proof);
        }
        $user = User::create([
                'username' => $data['username'],
                'password' => $data['password'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'mobile_number' => $data['mobile_number'],
                'office_type' => $data['office_type'],
                'office_id' => $data['office_id'],
                'password' => Hash::make($data['password']),
                'current_bank_name' => $data['current_bank_name'],
                'branch_name' => $data['branch_name'],
                 'account_number' => $data['account_number'],
                 'ifsc_code' => $data['ifsc_code'],
                 'avatar' => $avatarimage,
                 'doc_proof' => $doc_proof,
        ]);
        UserRole::create([
                 'user_id' => $user->id,
                 'role_id' => $data['role_id']
        ]);
        return redirect()->route('employees.index');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        $employee = User::with('user_role')->find($id);
       
        $type = $employee->office_type;
        $branches = Branch::get();
        $roles = Role::get();
        if($type == 'FR'){
            $branches = Franchisee::get();
        }



        return view('employees.edit',compact('employee', 'branches', 'roles'));
    }

    /** Employee View */
    public function view($id){
        $employee = User::with('user_role')->find($id);
       
        $type = $employee->office_type;
        if($employee->office_type == 'BR'){
             $branches = Branch::where('id','=', $employee->office_id)->first();
        }
        $roles = Role::get();
        if($type == 'FR'){
            $branches = Franchisee::where('id','=', $employee->office_id)->first();
        }
        // echo '<pre>';
        // print_r($branches);exit;
        return view('employees.view',compact('employee', 'branches', 'roles'));

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
        $this->validate(
            $request, [
                 'office_type' => 'required',
                 'office_id' => 'required',
                 'role_id' => 'required',
                 'first_name' => 'required|regex:/^[a-zA-Z ]+$/',
                 'last_name' => 'required|regex:/^[a-zA-Z ]+$/',
                 'username' => 'required',
                 'email' => 'email|max:255|unique:users,email,'.$id,
                 'mobile_number' => 'required|digits:10|numeric|unique:users,mobile_number,'.$id,
                 'current_bank_name' => '',
                 'branch_name' => '',
                 'account_number' => '',
                 'ifsc_code' => '',
                 'avatar' => 'image|mimes:jpeg,png,jpg|max:4028',
                 'doc_proof' => 'image|mimes:jpeg,png,jpg|max:4028',
              
                ]
        );
        $avatarimage = null;
        if($request->input('old_avatar') !=''){
              $avatarimage = $request->input('old_avatar');
        }
       
        if (request()->hasFile('avatar')) {
            Storage::delete('/public/uploads/employees/photo' . $avatarimage);
            $profile = request()->file('avatar');
            $avatarimage = md5($profile->getClientOriginalName() . time()) . "." . $profile->getClientOriginalExtension();
            $profile->move('./storage/uploads/employees/photo', $avatarimage);
        }
        $doc_proof = null;
        if($request->input('old_doc_proof') !=''){
            $doc_proof = $request->input('old_doc_proof');
        }
       
        if (request()->hasFile('doc_proof')) {
            Storage::delete('/public/uploads/employees/idproof' . $doc_proof);
            $doc = request()->file('doc_proof');
            $doc_proof = md5($doc->getClientOriginalName() . time()) . "." . $doc->getClientOriginalExtension();
            $doc->move('./storage/uploads/employees/idproof', $doc_proof);
        }
           
           $user = User::find($id);
           $user->fill([
            'password' => Hash::make($request->input('new_password'))
            ])->save();

       
           $user->office_type = $request->input('office_type');
           $user->office_id = $request->input('office_id');
           $user->first_name = $request->input('first_name');
           $user->last_name = $request->input('last_name');
           $user->username = $request->input('username');
           $user->email = $request->input('email');
           $user->current_bank_name = $request->input('current_bank_name');
           $user->branch_name = $request->input('branch_name');
           $user->account_number = $request->input('account_number');
           $user->ifsc_code = $request->input('ifsc_code');
           $user->avatar = $avatarimage;
           $user->doc_proof = $doc_proof;
           $user->save();

        $user->user_role()->delete();
        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id')
   ]);
        return redirect()->route('employees.index')->with('success', 'Employee has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully');
    }

    public function findBranch(Request $request){

        $type = $_GET['type'];

         $term = trim($request->q);

         if (empty($term)) {
             return \Response::json([]);
         }
         if($type == 'FR'){
            $branches = Franchisee::where('code', 'LIKE', "%$term%")->get();
         }else{
            $branches = Branch::where('code', 'LIKE', "%$term%")->where('branch_type','=',$type)->get();
         }


         $_branches = [];

         foreach ($branches as $branch) {
             $_branches[] = ['id' => $branch->id, 'text' => $branch->code];
         }

         return \Response::json($_branches);
    }
    public function selectedBranch(Request $request){

         $type = $_GET['type'];

         if($type == 'FR'){
            $branches = Franchisee::get();
         }else{
            $branches = Branch::where('branch_type','=',$type)->get();
         }


         $_branches = [];

         foreach ($branches as $branch) {
             $_branches[] = ['id' => $branch->id, 'text' => $branch->code];
         }

         return \Response::json($_branches);
    }
}
