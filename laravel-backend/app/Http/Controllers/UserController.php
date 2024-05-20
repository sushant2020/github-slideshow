<?php

namespace App\Http\Controllers;

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

/**
 * Controller to manage functionality of create user, view user, edit-update user and activate-deactivate users
 *
 */
class UserController extends BaseController
{

	
	 public function getMail()
    {
      print_r("is in");
   
      $data = [];
      

      
      $sent =  \Mail::send('emails.test', ['data' => $data], function ($message)  {
                        $message->to('sushant@webdezign.co.uk', 'SChari')->subject('Test');
                    });
      if( ! $sent) { dd($sent);
                   } else {
			dd("send");
      }
    }
	
    /**

     * User Profile Api
     *
     * *@param \Illuminate\Http\Request $request
     *
     * @return json The Current Userinformation that have set while login
     *
     */
    public function profile(Request $request)
    {
		$user=[];
        try {

            
//            $userId = Auth::user()->id;
//           
//            
//            $user = User::where(["id" => $userId])->first();

			
            $userId = Auth::user()->id;
            $user1 = User::where(["id" => $userId])->first();
			$userRole = Auth::user();
			$roles = !empty($userRole) ? $userRole->getRoleNames()->toArray() : [];
			$user=["userProfle"=>$user1,"role"=>$roles];

            return $this->sendResponse($user, "Showing User Profile");
        } catch (\Exception $error) {
            return $this->sendError('Failed to view user profile', ['error' => 'Failed : ' . $error->getMessage()], 500);
        }
    }

    /**
     * Updates user profile
     *
     * @param  \Illuminate\Http\Request $request

     *
     *
     */
    public function store(Request $request)
    {

        //if (!Gate::allows('users_manage')) {
        //return abort(401);
        //}
        $requestData = $request->all();
	
        //Server side validations
        $validator = Validator::make($requestData, [
                    'firstname' => 'required',
                    'lastname' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }
        $firstname = !empty($requestData['firstname']) && isset($requestData['firstname']) ? $requestData['firstname'] : "";
        $lastname = !empty($requestData['lastname']) && isset($requestData['lastname']) ? $requestData['lastname'] : "";

        try {
            $userId = Auth::user()->id;
            $user = User::where(["id" => $userId])->first();

            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->updated_at = \Carbon\Carbon::now();
            $user->lastchanged_by = Auth::user()->id;
            $user->save();
            return $this->sendResponse($user->id, "Profile updated successfully.");
        } catch (\Exception $error) {
            return $this->sendError('Failed to update profile', ['error' => 'Failed : ' . $error->getMessage()]);
        }
    }
    
    

}
