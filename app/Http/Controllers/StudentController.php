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

           return view('student-create', [
               'result' => 'Please fill up all the fields1'
           ]);  

        }else if(!$request->has('user_name') || empty($request->input('user_name'))) {

              return view('student-create', [
                'result' => 'Please fill up all the fields2'
            ]); 

        }else if(!$request->has('password') || empty($request->input('password'))) {

             return view('student-create', [
                'result' => 'Please fill up all the fields3'
             ]); 

        }else if(!$request->has('first_name') || empty($request->input('first_name'))) {

             return view('student-create', [
                'result' => 'Please fill up all the fields4'
             ]); 

        }else if(!$request->has('last_name') || empty($request->input('last_name'))) {

             return view('student-create', [
                'result' => 'Please fill up all the fields5'
             ]); 
        
        }
        
        if($this->checkIfUsernameExist($request->input('user_name'))){
            return view('student-create', [
                'result' => 'Username Already exists'
            ]);
        }

        if($this->checkIfStudentIdExist($request->input('student_id'))){
           return view('student-create', [
              'result' => 'Student ID Already exists'
           ]);
        }

        $user = new User;
        $student = new Student;

        $user->user_name = $request->input('user_name');
        $user->password = $request->input('password');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->user_type = 2;
        $user->save();

        $student->student_id = $request->input('student_id');
        $student->user_no = $user->user_no;
        $student->save();

        return view('student-create', [
            'result' => 'User Saved'
        ]);

    }

    public function edit(Request $request){
     
        #Check if Request is empty
        if(!$request->has('student_id') || empty($request->input('student_id'))) {

           return view('student-edit', [
               'result' => 'Please fill up the fields',
               'student_info' => StudentController::getStudentInfo($request->input('user_no'))
           ]);  

        }else if(!$request->has('user_name') || empty($request->input('user_name'))) {

              return view('student-edit', [
                'result' => 'Please fill up the fields',
                'student_info' => StudentController::getStudentInfo($request->input('user_no'))
            ]); 

        }else if(!$request->has('password') || empty($request->input('password'))) {

             return view('student-edit', [
                'result' => 'Please fill up the fields',
                'student_info' => StudentController::getStudentInfo($request->input('user_no'))
             ]); 

        }else if(!$request->has('first_name') || empty($request->input('first_name'))) {

             return view('student-edit', [
                'result' => 'Please fill up the fields',
                'student_info' => StudentController::getStudentInfo($request->input('user_no'))
             ]); 

        }else if(!$request->has('last_name') || empty($request->input('last_name'))) {

             return view('student-edit', [
                'result' => 'Please fill up the fields',
                'student_info' => StudentController::getStudentInfo($request->input('user_no'))
             ]); 
        
        }
        
        if($this->checkIfUsernameExist($request->input('user_name'), $request->input('user_no'))){
            
            return view('student-edit', [
                'result' => 'Username Already Exist',
                'student_info' => StudentController::getStudentInfo($request->input('id'))
            ]);

        }

        if($this->checkIfStudentIdExist($request->input('student_id'), $request->input('user_no'))){

           return view('student-edit', [
               'result' => 'Student ID Already Exist',
               'student_info' => StudentController::getStudentInfo($request->input('id'))
           ]);
        }

        $user = new User;

        $user->where('user_no', $request->input('user_no'))
        ->update([
            'user_name' => $request->input('user_name'),
            'password' => $request->input('password'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ]);

        $student = new Student;

        $student->where('user_no', $request->input('user_no'))
        ->update([
         'student_id' => $request->input('student_id')
        ]);

        return view('student-edit', [
            'result' => 'Saved',
            'student_info' => StudentController::getStudentInfo($request->input('user_no'))
        ]);

    }

    public function delete(Request $request){
        $user = new User;
        $student = new Student;
        $plotted_classes = new PlottedClasses;

        $user->where('user_no', '=', request('id'))->delete();
        $student->where('user_no', '=', request('id'))->delete();
        $plotted_classes->where('user_no', '=', request('id'))->delete();

        return redirect()->route('student-list');
    }

    public function createWithCSV(Request $request){
        if($request->hasFile('csvFile')){

            $csvValues = explode(',',$request->file('csvFile')->get());
            $count = count($csvValues);

            for($i = 0; $i < $count; $i++){
                if(empty($csvValues[$i])){
      
                    return redirect()->route('student-list', [
                        'result' => 'One of the values is missing, please check the file'
                    ]);

                }
            }

            if($this->checkIfUsernameExist($csvValues[1])){
                return redirect()->route('student-list', [
                    'result' => 'Username Already exists'
                ]);
            }
    
            if($this->checkIfStudentIdExist($csvValues[0])){
                return redirect()->route('student-list', [
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

            return redirect()->route('student-list', [
                'result' => 'User Saved'
            ]);

        }else{
            
            return redirect()->route('student-list', [
                'result' => 'File does not exist'
            ]);

        }
    }

    public function checkIfUsernameExist($user_name, $id = null){
        $user_model = new User;

        if($id === null){
        
            $get_duplicates = $user_model::where('user_name', $user_name)->get();

            if($get_duplicates->count() > 0){
                return true;
            }

        }else{

            $get_duplicates = $user_model::where('user_name', $user_name)->where('user_no', '!=', $id)->get();

            if($get_duplicates->count() > 0){
                return true;
            }

        }

        return false;
    }

    public function checkIfStudentIdExist($student_id, $id = null){
        $student_model = new Student;

        if($id === null){

           $get_duplicate = $student_model::where('student_id', $student_id)->get();

           if($get_duplicate->count() > 0){
               return true;
           }

        }else{
  
            
            $get_duplicate = $student_model::where('student_id', $student_id)->where('user_no', '!=', $id)->get();

            if($get_duplicate->count() > 0){
                return true;
            }  

        }

        return false;
    }

    public function show($request, $teacher_select_options, $class_select_options, $result){
        $student = new Student;


        if($request->has('srchStudentByName') && 
        $request->has('srchStudentByClass') && 
        $request->has('srchStudentByTeacher') ) {

            if(empty($request->input('srchStudentByName')) || 
            empty($request->input('srchStudentByClass')) || 
            empty($request->input('srchStudentByTeacher'))) {

                $student_list = $student->select('student.student_id', 'userA.first_name as student_first_name', 'userA.last_name as student_last_name', 'classes.classes_name', 'userB.first_name as teacher_first_name', 'userB.last_name as teacher_last_name')
                ->join('user as userA', 'userA.user_no', '=', 'student.user_no')
                   ->join('plotted_classes as plot_classA', 'plot_classA.user_no', '=', 'student.user_no')
                   ->join('classes', 'classes.classes_no', '=', 'plot_classA.classes_no')
                   ->join('plotted_classes as plot_classB', 'plot_classB.classes_no', '=', 'plot_classA.classes_no')
                   ->join('teacher', 'teacher.user_no', '=', 'plot_classB.user_no')
                   ->join('user as userB', 'userB.user_no', '=', 'teacher.user_no')->paginate(3);

            }else if(!empty($request->input('srchStudentByName')) || 
            empty($request->input('srchStudentByClass')) || 
            empty($request->input('srchStudentByTeacher'))) {

                $student_list = $student->where('');

            }

           return view('student', [
              'result' => $result,
              'teacher_options' => $teacher_select_options,
              'class_options' => $class_select_options,
              'student_table_results' => $student_list
           ]);

        }

        $student_list = $student->select('student.user_no','student.student_id', 'user.first_name as student_first_name', 'user.last_name as student_last_name')
            ->join('user as user', 'user.user_no', '=', 'student.user_no')->paginate(3);

        return view('student', [
           'result' => $result,
           'teacher_options' => $teacher_select_options,
           'class_options' => $class_select_options,
           'student_table_results' => $student_list
        ]);
    }

    public function getStudentInfo($id){
        $student = new Student;

        return $student->join('user', 'user.user_no', '=', 'student.user_no')->where('student.user_no', '=', $id)->first();

    }
}
