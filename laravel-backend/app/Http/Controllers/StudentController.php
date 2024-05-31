<?php

namespace App\Http\Controllers;
use Exception;



class StudentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
      try {
            \Mail::raw('This is a test email', function ($message) {
                $message->to('sushant@webdezign.co.uk')
                        ->subject('Test Email');
            });

            return response()->json('Great! Successfully sent the mail');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    
               
    }
}