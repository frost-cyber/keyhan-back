<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller {
    public function index() {
        return Role::all();
    }

    public function store( Request $request ) {
        $this->validateRoleRequest( $request );
        return response([
            'message' => 'Update Successfully',
            'role' => $this->saveRole($request->all()),
        ]);
    }

    public function show( Role $role ) {
        return $role->load(['permissions' => function($query){
            return $query->select('name');
        }]);
    }

    public function update( Request $request, Role $role ) {
        $this->validateRoleRequest( $request, $role->id );
        return response([
            'message' => 'Update Successfully',
            'role' => $this->saveRole($request->all() , $role),
        ]);
    }


    public function destroy( Role $role ) {
        $role->delete();
        return response('Delete Successfully');
    }

    protected function validateRoleRequest( Request &$request, $exceptId = null ) {
        $request->validate( [
            'name'          => 'required|unique:roles,name' . ( $exceptId ? ( ',' . $exceptId ) : '' ),
            'permissions'   => 'array',
            'permissions.*' => 'exists:permission,name'
        ] );
    }

    protected function saveRole( $roleData, Role $role = null ) {
        if (!$role) $role = new Role();
        $role->name = $roleData['name'];
        $role->save();
        $role->syncPermissions($roleData['permissions']);
        return $role;
    }
}
