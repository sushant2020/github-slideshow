<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoginController extends BaseController
{

    /**

     * Login api

     *

     * @return \Illuminate\Http\Response

     */
    public function login(Request $request)
    {
        //Server side validations
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }

        $user = User::where('email', $request->email)->first();
      
        if (!$user) {  // if email does not find in database
            return $this->sendErrorResponse('User Not Found.', 'The user account does not exist in the system', 203);
        }

        if (!empty($user)) {
            if ($user->is_deleted == 1) {
                return $this->sendErrorResponse('User Archived.', 'The user account is disabled or archived', 202);
            }
 
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();

                if ($user->password_changed == null) {  //check user login first time and set flag 1 after first login
                    $user->password_changed = 1;
                    $user->save();

                    $success['first_attempt'] = "true";
                }
                $roles = !empty($user) ? $user->getRoleNames()->toArray() : [];
                $token = trim($user->createToken('ProductPortal')->accessToken, '"');

                $success['token'] = $token;

                $success['name'] = !empty($user) ? $user->getName() : "";
                $success['email'] = !empty($user) ? $user->email : "";
                $success['created_at'] = !empty($user) ? $user->created_at : "";
                $success['roles'] = $roles;
                return $this->sendResponse($success, 'User login successfully.');
            } else {
                return $this->sendErrorResponse('Unauthorised.', 'Please enter your correct password and try again.', 201);
            }
        }
    }

    /**
     * Logout Auth User
     *
     * @param Request $request
     * @return void
     */
    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->token()->revoke();
            return $this->sendResponse("", 'Success! You are logged out');
        }
        return $this->sendError('failed.', ['error' => 'Failed! You are already logged out.'], 403);
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
                    'email' => 'required|email',
                    'password' => 'required',
        ]);
    }

}
