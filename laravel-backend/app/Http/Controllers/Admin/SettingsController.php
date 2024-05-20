<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Setting;
use Exception;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
/**
 * Controller to manage functionality of create user, view user, edit-update user and activate-deactivate users
 *
 */
class SettingsController extends BaseController
{

    /**

     * Admin Settings list
     *
     *
     * @return json show settings
     *
     */
    public function index()
    {
        try {
            $settings = Setting::all();
           
          
            $response = ["settings" => $settings];

            return $this->sendResponse($response, 'Settings showing successfully.');
        } catch (\Exception $error) {
            return $this->sendError('No any settings found', ['error' => $error->getMessage()]);
        }
    }



    /**

     * API to update admin settings
     *
     * @param \Illuminate\Http\Request $request
   
     *
     * @return json Update The User Information
     *
     */
    public function update(Request $request)
    {
       

        //Server side validations
        $validator = Validator::make($request->all(), [
                    'admin_email' => 'required',
                    'visited_pline_default_expiry' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }


        try {
			$requestData = $request->all();
			
			
              foreach($requestData as $meta_key =>  $value) {
				
             	DB::table('settings')
                        ->where(["meta_key" => $meta_key])
                        ->update(["meta_value" => $value,"updated_datetime" => \Carbon\Carbon::now(),"updated_by" => Auth::user()->id]);
            }
            return $this->sendResponse('Settings Updated', 'Settings updated successfully.');
        } catch (\Exception $error) {
            $user->delete();
            return $this->sendErrorResponse('Failed to ettings user', 'Failed : ' . $error->getMessage(), 204);
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
