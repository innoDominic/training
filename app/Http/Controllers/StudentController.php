<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Student;
use App\Models\PlottedClasses;

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
        
        if($this->checkIfUsernameExist($request->input('user_name'))){
            return view('admin', [
               'page' => 'student-create',
                'result' => 'Username Already exists'
            ]);
        }

        if($this->checkIfStudentIdExist($request->input('student_id'))){
           return view('admin', [
              'page' => 'student-create',
              'result' => 'Student ID Already exists'
           ]);
        }

        $user = new User;
        $student = new Student;

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

    public function createWithCSV(Request $request){
        if($request->hasFile('csvFile')){

            $csvValues = explode(',',$request->file('csvFile')->get());
            $count = count($csvValues);

            for($i = 0; $i < $count; $i++){
                if(empty($csvValues[$i])){
      
                 return redirect()->route('admin', [
                     'page' => 'student',
                     'result' => 'One of the values is missing, please check the file'
                 ]);

                }
            }

            if($this->checkIfUsernameExist($csvValues[1])){
                return redirect()->route('admin', [
                    'page' => 'student',
                    'result' => 'Username Already exists'
                ]);
            }
    
            if($this->checkIfStudentIdExist($csvValues[0])){
                return redirect()->route('admin', [
                    'page' => 'student',
                    'result' => 'Student ID Already exists'
                 ]);
            }
    
            $user = new User;
            $student = new Student;
    
            $user->user_name = $csvValues[1];
            $user->password = Hash::make($csvValues[4]);
            $user->first_name = $csvValues[2];
            $user->last_name = $csvValues[3];
            $user->user_type = 2;
            $user->save();
    
            $student->student_id = $csvValues[0];
            $student->user_no = $user->user_no;
            $student->save();

            return redirect()->route('admin', [
                'page' => 'student',
                'result' => 'User Saved'
            ]);

        }else{
            
            return redirect()->route('admin', [
                'page' => 'student',
                'result' => 'File does not exist'
            ]);

        }
    }

    public function checkIfUsernameExist($user_name){
        $user_model = new User;

        $get_duplicates = $user_model::where('user_name', $user_name)->get();

        if($get_duplicates->count() > 0){
            return true;
        }

        return false;
    }

    public function checkIfStudentIdExist($student_id){
        $student_model = new Student;

        $get_duplicate = $student_model::where('student_id', $student_id)->get();

        if($get_duplicate->count() > 0){
            return true;
        }

        return false;
    }

    public function show(Request $request){
        if($request->has('srchStudentByName') || $request->has('srchStudentByClass') || $request->has('srchStudentByTeacher') ) {
            
            $teachers = Teacher::join('user', 'user.user_no', '=', 'teacher.user_no')->get();

        }
    }
}
