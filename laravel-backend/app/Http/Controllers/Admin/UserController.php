<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Components\Helper;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
/**
 * Controller to manage functionality of create user, view user, edit-update user and activate-deactivate users
 *
 */
class UserController extends BaseController
{

    /**

     * User Api Shows List of all Users Which are present in system
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json Show All Users
     *
     */
    public function index()
    {
     
        try {
            $users = User::with('roles:id,name')->get();
           
            $roles = DB::table('roles')->select('id', 'name')->get();
            $response = ["user_with_role" => $users, "all_roles" => $roles];

            return $this->sendResponse($response, 'Users fetched successfully.');
        } catch (\Exception $error) {
            return $this->sendError('No Any User Found', ['error' => $error->getMessage()]);
        }
    }

    /**

     * Create User Api Accept all the required field and store user in Database and send Email Notification
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return json Store The User information in database
     *
     */
    public function store(Request $request)
    {
        //Server side validations
        $requestData = $request->all();
		
        $validator = $this->validator($requestData);

        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }

        //Gets request data
        $firstname = !empty($requestData['firstname']) && isset($requestData['firstname']) ? $requestData['firstname'] : "";
        $lastname = !empty($requestData['lastname']) && isset($requestData['lastname']) ? $requestData['lastname'] : "";
        $email = !empty($requestData['email']) && isset($requestData['email']) ? $requestData['email'] : "";
        $insertedby = $lastchanged_by = Auth::user()->id;
        $password = Helper::getRandomPassword();
			
        $currentDateTime = \Carbon\Carbon::now();
        $user = null;
		
        //Creates User record
        try {
            $user = User::create([
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                        'inserted_by' => $insertedby,
                        'lastchanged_by' => $lastchanged_by
            ]);
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to create user', 'Failed : ' . $error->getMessage(), 204);
        }

        if ($user) {
            $name = $user->getName();
		
            $rolesStr = "";

            $roles = !empty($requestData['roles']) && isset($requestData['roles']) ? $requestData['roles'] : "";
            $rolesStr = !empty($roles) ? implode(",", $roles) : "";
            $data = ["roles" => $rolesStr, "username" => $email, "password" => $password, "name" => $name];

            try {
		
                //Assign Role/s to User. A user can have one or multiple roles
	
                $user->assignRole($roles);

                //Sends Notification email to user
                $sendData = ["name" => $name, "email" => $email];
			
                try {
                    \Mail::send('emails.new_user_creation', ['data' => $data], function ($message) use ($sendData) {
                        $message->to($sendData["email"], $sendData["name"])
                                 ->cc('sushant@webdezign.co.uk', 'Sushant')
                                ->subject('Your account is created');
                    });

                    $success['token'] = $user->createToken('ProductPortal')->accessToken;
                    $success['name'] = $user->getName();
						
                    return $this->sendResponse($success, 'User Created successfully.');
                } catch (\Exception $error) {
                   // $user->delete();
                    return $this->sendErrorResponse('Could not send email to user', 'Failed : ' . $error->getMessage(), 204);
                }
            } catch (\Exception $error) {
                $user->delete();
                return $this->sendErrorResponse('Failed to create user', 'Failed : ' . $error->getMessage(), 204);
            }
        }
    }

    /**

     * API to update user details and roles
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     *
     * @return json Update The User Information
     *
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendErrorResponse('User not found', 'User does not exist', 204);
        }

        //Server side validations
        $validator = Validator::make($request->all(), [
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'roles' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }


        try {
            $user->lastchanged_by = Auth::user()->id;
            $user->update($request->all());
            $roles = $request->input('roles') ? $request->input('roles') : [];

            $user->syncRoles($roles);
            return $this->sendResponse($user->id, 'User updated successfully.');
        } catch (\Exception $error) {
           // $user->delete();
            return $this->sendErrorResponse('Failed to update user', 'Failed : ' . $error->getMessage(), 204);
        }
    }

    /**

     * Api to activate and Deactivate user
     *
     * @param $id
     *
     * @return json Activate or Deactivate User
     *
     */
    public function destroy($id)
    {
        $user = User::find($id);

        //Checks if user exists
        if (is_null($user)) {
            return $this->sendErrorResponse('User not found', 'User does not exist', 204);
        }

        if (!empty($user)) {
            if ($user->is_deleted == 1) {
                $user->is_deleted = 0;   //if user is deactive, activate it
                $user->updated_at = \Carbon\Carbon::now();
                $user->lastchanged_by = Auth::user()->id;
                $message = 'User activated successfully.';
                $status = 'activate';
            } else {
                $user->is_deleted = 1; //if user is active, deactivate it
                $user->updated_at = \Carbon\Carbon::now();
                $user->lastchanged_by = Auth::user()->id;
                $message = 'User deactivated successfully.';
                $status = 'deactivate';
            }
            $user->save();
            return $this->sendResponse($status, $message);
        }
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
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'email' => 'required|email|unique:users',
                    'roles' => 'required'
        ]);
    }

}
