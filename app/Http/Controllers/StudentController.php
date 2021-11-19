<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Student;

class StudentController extends Controller
{
   public function create(Request $request){
    
       #Check if Request is empty
       if(!$request->has('student_id') || empty($request->input('student_id'))) {

          return view('admin', [
               'page' => 'student-create',
                'result' => 'Please fill up all the fields1'
          ]);  

       }else if(!$request->has('user_name') || empty($request->input('user_name'))) {

             return view('admin', [
                 'page' => 'student-create',
               'result' => 'Please fill up all the fields2'
           ]); 

       }else if(!$request->has('password') || empty($request->input('password'))) {

            return view('admin', [
               'page' => 'student-create',
               'result' => 'Please fill up all the fields3'
            ]); 

       }else if(!$request->has('first_name') || empty($request->input('first_name'))) {

            return view('admin', [
               'page' => 'student-create',
               'result' => 'Please fill up all the fields4'
            ]); 

       }else if(!$request->has('last_name') || empty($request->input('last_name'))) {

            return view('admin', [
               'page' => 'student-create',
               'result' => 'Please fill up all the fields5'
            ]); 
       
       }

       $user = new User;
       $student = new Student;

       $get_username_duplicate = $user::where('user_name', $request->input('user_name'))->get();
       $get_student_id_duplicate = $student::where('student_id', $request->input('student_id'))->get();

       if($get_username_duplicate->count() > 0){
           return view('admin', [
             'page' => 'student-create',
             'result' => 'Username Already exists'
          ]);
       }

       if($get_student_id_duplicate->count() > 0){
           return view('admin', [
             'page' => 'student-create',
             'result' => 'Student ID Already exists'
          ]);
       }

       $user->user_name = $request->input('user_name');
       $user->password = Hash::make($request->input('password'));
       $user->first_name = $request->input('first_name');
       $user->last_name = $request->input('last_name');
       $user->user_type = 2;
       $user->save();

       $student->student_id = $request->input('student_id');
       $student->user_no = $user->user_no;
       $student->save();

       return view('admin', [
           'page' => 'student-create',
           'result' => 'User Saved'
       ]);

   }

   private function ifValuesExists(){

   }
}
