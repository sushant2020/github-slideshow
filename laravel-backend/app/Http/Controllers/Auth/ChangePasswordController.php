<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Hash;
use Validator;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController as BaseController;

class ChangePasswordController extends BaseController
{

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * API to change password
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $validator = $this->validator($request->all());
		$errorString = '';
        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
		
            $errorString = $this->errorString($errors);
		
			 return $this->sendErrorResponse('Failed to change password', $errorString, 208);
      	
        } 

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                "password" => Hash::make($request->password)
            ]);
            $user->updated_at = \Carbon\Carbon::now();
            $user->lastchanged_by = Auth::user()->id;
            $user->save();
            return $this->sendResponse("Changed Password", 'Password changed successfully.');
        } else {
            return $this->sendErrorResponse('Failed to change password', 'Current password is incorrect', 204);
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
                    'old_password' => 'required',
                    'password' => 'required|min:6|max:100',
                    'confirm_password' => 'required|same:password'
        ]);
    }

}
