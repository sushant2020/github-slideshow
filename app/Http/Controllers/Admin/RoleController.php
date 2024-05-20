<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;
use App\Http\Controllers\BaseController as BaseController;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Validation\Rule;

/* * Controller to create role, assign permissions to role,
 * edit role, view role details and activate-deactivate role
 */

class RoleController extends BaseController
{

    /**
     * API for role listing
     *
     *
     * @return json Json array of tag items
     *
     */
    public function index()
    {
		 
        $response = [];
        $role_permissions = Role::select('id', 'name', 'description', 'created_at', 'updated_at', 'is_deleted')->with('permissions:id,name')->get();

        $permission = DB::table('permissions')->select('id', 'name')->get();
        $response = ["role_with_permission" => $role_permissions, "all_permissions" => $permission];

        if (!empty($response)) {
            return $this->sendResponse($response, 'Roles fetched successfully.');
        } else {
            return $this->sendError('No Any Role Found', ['error' => "No Any Role Found"]);
        }
    }

    /**
     * Api to Create Role
     *
     * @param \illuminate\Http\Request $request
     *
     * @return json Json
     *
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', 'Failed :' . $errorString, 204);
        } 
        $role = null;

        try {
            $permissions = $request->input('permission') ? $request->input('permission') : [];

            $insertedby = $lastchanged_by = Auth::user()->id;
            $request["guard_name"] = "web";
            $request["inserted_by"] = $insertedby;
            $request["lastchanged_by"] = $lastchanged_by;
            $role = Role::create($request->except('permission'));

            $role->givePermissionTo($permissions);

            return $this->sendResponse($role->id, 'Role created successfully.');
        } catch (\Exception $error) {
            if (!empty($role)) {
                $role->delete();
            }
            return $this->sendErrorResponse('Failed to create role', 'Failed :' . $error->getMessage(), 204);
        }
    }

    /**
     * Update Role Api
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id roleid
     *
     * @return json Json

     */
    public function update(Request $request, $id)
    {
	
        $role = Role::find($id);
		
        if (is_null($role)) {
            return $this->sendErrorResponse('Role not found', 'Role does not exist', 204);
        }
        //Server side validations
        $validator = Validator::make($request->all(), [
                    'name' => 'unique:roles,name,' . $role->id,
                    "permission" => "required",
        ]);

        //$this->validator($request->all());
        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', 'Error: ' . $errorString, 204);
        }

        try {
            $role->lastchanged_by = Auth::user()->id;
            $role->update($request->except('permission'));
            $permissions = $request->input('permission') ? $request->input('permission') : [];
            $role->syncPermissions($permissions);
            return $this->sendResponse("Updated Role", 'Role updated successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update role', 'Failed: ' . $error->getMessage(), 204);
        }
    }

    /**
     * Api to Activate and Deactivate Role
     *
     * @param int $id Role ID
     *
     * @return json Json
     *
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (is_null($role)) {
            return $this->sendErrorResponse('Role not found', 'Role does not exist', 204);
        }
        if (!empty($role)) {
            if ($role->is_deleted == 1) {
                $role->is_deleted = 0;   //if role deactivate activate it
                $role->updated_at = \Carbon\Carbon::now();

                $message = 'Role activated successfully.';
                $status = 'activate';
            } else {
                $role->is_deleted = 1; //if role activate deactivate it
                $role->updated_at = \Carbon\Carbon::now();
                $message = 'Role deactivated successfully.';
                $status = 'deactivate';
            }
            $role->save();
            return $this->sendResponse($status, $message);
        }
    }

    /**
     * view single role Api
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param int $id roleid
     *
     * @return \Illuminate\Http\Response

     */
    public function show($id)
    {
        $role = Role::find($id);

        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $role)
                ->get();

        return response()->json(["success" => "viewroledetails", "data" => $rolePermissions], 200);
    }

    /**
     * Get a validator for an incoming change password request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
                    'name' => 'required|unique:roles',
                    "permission" => "required",
        ]);
    }

}
