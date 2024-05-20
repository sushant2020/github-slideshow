<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController as BaseController;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Controller to build functionalies of send reset password link email andreset password
 *
 */
class ForgotPasswordController extends BaseController
{
    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset emails and
      |
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * API to send reset password link
     *
     * @return json
     */
    public function forgot()
    {
        try {
            $credentials = request()->validate(['email' => 'required|email|exists:users']);

            Password::sendResetLink($credentials);
            return $this->sendResponse("Reset Password", 'Reset password link sent on your email id.');
        } catch (\Exception $error) {
            return $this->sendError('Failed to send reset password email', ['error' => 'Failed : ' . $error->getMessage()]);
        }
    }

    /**
     * API to reset password
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return json
     */
    public function reset(Request $request)
    {

        //Server side validation
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|exists:users',
                    'token' => 'required|string',
                    'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'min:6|required'
        ]);

        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];

            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', 'Validation Error : ' . $errorString, 208);
        }
        //Initialization of variables
        $success = [];
        $token = '';

        try {
            //Update password of user
            $user = User::where('email', $request->email)->first();
            $credentials = request()->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|confirmed'
            ]);
            $reset_password_status = Password::reset($credentials, function ($user, $password) {
                        $user->update(['password' => Hash::make($password)]);
                    });

            $roles = !empty($user) ? $user->getRoleNames()->toArray() : [];
            $token = trim($user->createToken('ProductPortal')->accessToken, '"');

            //Build success response
            $success['token'] = $token;
            $success['name'] = !empty($user) ? $user->getName() : "";
            $success['email'] = !empty($user) ? $user->email : "";
            $success['created_at'] = !empty($user) ? $user->created_at : "";
            $success['roles'] = $roles;

            //Checks if token is valid
            if ($reset_password_status == Password::INVALID_TOKEN) {
                return $this->sendErrorResponse('Invalid token', 'Invalid token provided', 208);
            }

            return $this->sendResponse($success, 'Password has been successfully changed');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to reset password', $error->getMessage(), 208);
        }
    }

}
