<?php

namespace App\Http\Controllers;




class StudentController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
       $data['success'] = TRUE;
       $students = [[
        "id" => 1,
        "student" => "Sergey",
        "subject" => "History",
        "mark" => 4,
    ],
    [
        "id" => 2,
        "student" => "Sergey",
        "subject" => "English",
        "mark" => 3,
    ]];
       $data['data'] = ['students' => $students];
       $data['message'] = 'Showing students';
       
     return response()->json($data, 200);
       
               
    }
}