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
        return $this->sendResponse('test', 'User login successfully.');
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
