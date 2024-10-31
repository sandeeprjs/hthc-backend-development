<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Module;
use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'role']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(10);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $modules = Module::whereNull('parent_id')->with('children')->get();
//        return response()->json($modules);

        return view('roles.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $role = new Role();
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        foreach ($request->input('permissions') as $key => $item) {

            $module = Module::select(['id'])->where('name', '=', $key)->first();

            $permission = new Permission();
            $permission->role_id = $role->id;
            $permission->module_id = $module->id;
            $permission->create = $item['create'] ?? 0;
            $permission->read = $item['view'] ?? 0;
            $permission->update = $item['edit'] ?? 0;
            $permission->delete = $item['delete'] ?? 0;
            if ($permission->create || $permission->update || $permission->read || $permission->delete) {
                $permission->enabled = 1;
            } else {
                $permission->enabled = 0;
            }
            $permission->save();

            if (array_key_exists('child', $item)) {
                foreach ($item['child'] as $key2 => $child) {
                    $childModule = Module::select(['id', 'name'])->where('name', '=', $key2)->first();

                    $childPermission = new Permission();
                    $childPermission->role_id = $role->id;
                    $childPermission->module_id = $childModule->id;
                    $childPermission->create = $child['create'] ?? 0;
                    $childPermission->read = $child['view'] ?? 0;
                    $childPermission->update = $child['edit'] ?? 0;
                    $childPermission->delete = $child['delete'] ?? 0;
                    if ($childPermission->create || $childPermission->update || $childPermission->read || $childPermission->delete) {
                        $childPermission->enabled = 1;
                        $permission->enabled = 1;
                        $permission->read = 1;
                        $permission->save();
                    }
//                    else {
//                        $permission->enabled = 0;
//                    }
                    $childPermission->save();
                }
            }
        }

        return redirect(route('roles.index'))->withSuccess('Roles added successfully!');
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
    public function edit(Request $request, $id)
    {
        $role = Role::find($id);
        $segment = $request->segment(2);
        $module = Module::where('name', '=', $segment)->first();

        $modules = Module::where('active', 1)->with(['permissions' => function ($permission) use ($role) {
            $permission->where('role_id', $role->id);
        }, 'children.permissions' => function ($children) use ($role) {
            $children->where('role_id', $role->id);
        }])->get();

        $deletePermission = $role->hasDeletePermission($module->id);

        return view('roles.edit', compact(['role', 'modules', 'deletePermission']));
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
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->description = $request->input('description');
        $role->save();

        
    //    print_r($request->permissions);
       //exit();

        foreach ($request->input('permissions') as $key => $item) {

            $module = Module::select(['id'])->where('name', '=', $key)->first();
    
            $permission = Permission::updateOrCreate(
                ['role_id' => $role->id, 'module_id' => $module->id],
                [
                    'create' => $item['create'] ?? 0,
                    'read' => $item['view'] ?? 0,
                    'update' => $item['edit'] ?? 0,
                    'delete' => $item['delete'] ?? 0,
                ]
            );
            if ($permission->create || $permission->update || $permission->read || $permission->delete) {
                $permission->enabled = 1;
            } else {
                $permission->enabled = 0;
            }

            if (array_key_exists('child', $item)) {
                foreach ($item['child'] as $key2 => $child) {
                    $childModule = Module::select(['id', 'name'])->where('name', '=', $key2)->first();
               
                    $childPermission = Permission::updateOrCreate(
                        ['role_id' => $role->id, 'module_id' => $childModule->id],
                        [
                            'create' => $child['create'] ?? 0,
                            'read' => $child['view'] ?? 0,
                            'update' => $child['edit'] ?? 0,
                            'delete' => $child['delete'] ?? 0
                        ]
                    );
                    if ($child['create'] || $child['view'] || $child['edit'] || $child['delete']) {
                        $childPermission->enabled = 1;
                        $permission->enabled = 1;
                        $permission->read = 1;
                    }
                    else {
                        $childPermission->enabled = 0;
                    }
                    $childPermission->save();
                }
            }
            $permission->save();
        }

       return redirect(route('roles.index'))->withSuccess('Roles updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::destroy($id);

        return redirect(route('roles.index'))->withSuccess('Roles deleted successfully!');
    }
}
