<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::latest()->get();
            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('action', function($role){
                    $btn = '<a href="javascript:void(0)" data-id="'.$role->id.'" class="edit btn btn-info btn-sm editRole">Edit</a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="'.$role->id.'" class="delete btn btn-danger btn-sm deleteRole">Delete</a>';
                    return $btn;
                })
                ->addColumn('created_at', function ($user) {
                    return \Carbon\Carbon::parse($user->created_at)->format('d-m-Y H:i:s'); // اختر التنسيق الذي تريده
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $permissions = Permission::all();
        return view('roles.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
        $role->syncPermissions($permissions);

        return response()->json(['success' => 'Role created successfully.']);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json(['error' => 'Role not found.'], 404);
        }

        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name');
        $role->syncPermissions($permissions);

        return response()->json(['success' => 'Role updated successfully.']);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
