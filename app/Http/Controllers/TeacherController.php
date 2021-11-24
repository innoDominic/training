<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\PlottedClasses;

class TeacherController extends Controller
{
    
    public function create(Request $request){
     
        #Check if Request is empty
        $empty_field = $this->checkValuesIfEmpty($request);
        if( $empty_field !== null){
            return view('teacher-create', [
                'result' => 'Please fill up ' . $empty_field
            ]);
        }

        if($this->checkIfUsernameExist($request->input('user_name'))){
            return view('teacher-create', [
                'result' => 'Username Already exists'
            ]);
        }

        if($this->checkIfTeacherIdExist($request->input('teacher_id'))){
            return view('teacher-create', [
                'result' => 'Teacher ID Already exists'
             ]);
        }

        $user = new User;
        $teacher = new Teacher;

        $user->user_name = $request->input('user_name');
        $user->password = $request->input('password');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->user_type = 1;
        $user->save();

        $teacher->teacher_id = $request->input('teacher_id');
        $teacher->teacher_title = $request->input('title');
        $teacher->user_no = $user->user_no;
        $teacher->save();

        return view('teacher-create', [
            'result' => 'User Saved'
        ]);

    }

    public function edit(Request $request){
     
        #Check if Request is empty
        $empty_field = $this->checkValuesIfEmpty($request);
        if( $empty_field !== null){
            return view('teacher-edit', [
                'result' => 'Please fill up ' . $empty_field,
                'teacher_info' => StudentController::getStudentInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfUsernameExist($request->input('user_name'), $request->input('user_no'))){
            return view('teacher-edit', [
                'result' => 'Username Already exists',
                'teacher_info' => TeacherController::getTeacherInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfTeacherIdExist($request->input('teacher_id'), $request->input('user_no'))){
            return view('teacher-edit', [
                'result' => 'Student ID Already exists',
                'teacher_info' => TeacherController::getTeacherInfo($request->input('user_no'))
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

        $teacher = new Teacher;

        $teacher->where('user_no', $request->input('user_no'))
        ->update([
            'teacher_id' => $request->input('teacher_id'),
            'teacher_title' => $request->input('title')
        ]);

        return view('teacher-edit', [
            'result' => 'Saved',
            'teacher_info' => TeacherController::getTeacherInfo($request->input('user_no'))
        ]);

    }

    public function show(Request $request, $class_options, $result){
        
        $teacher = new Teacher;
        $teacher_list = $teacher->select('user.first_name', 'user.last_name', 'user.user_no', 'teacher.teacher_title', 'teacher.teacher_id')
            ->join('user', 'user.user_no', '=', 'teacher.user_no')->paginate(3);

        return view('teacher', [
            'result' => $result,
            'class_options' => $class_options,
            'teacher_table_results' => $teacher_list
        ]);

    }

    public function delete(Request $request){
        $user = new User;
        $teacher = new Teacher;
        $plotted_classes = new PlottedClasses;

        $user->where('user_no', '=', request('id'))->delete();
        $teacher->where('user_no', '=', request('id'))->delete();
        $plotted_classes->where('user_no', '=', request('id'))->delete();

        return redirect()->route('teacher-list');
    }

    public function checkValuesIfEmpty(Request $request){

        $input_names = ['teacher_id', 'user_name', 'password', 'title', 'first_name', 'last_name'];

        foreach($input_names as $name){
            if( !$request->has($name) || empty($request->input($name)) ){
                return $name;
            }
        }

        return null;
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

   public function checkIfTeacherIdExist($teacher_id, $id = null){
       $teacher = new Teacher;

       if($id === null){

          $get_duplicate = $teacher::where('teacher_id', $teacher_id)->get();

          if($get_duplicate->count() > 0){
              return true;
          }

       }else{

           $get_duplicate = $teacher::where('teacher_id', $teacher_id)->where('user_no', '!=', $id)->get();

           if($get_duplicate->count() > 0){
               return true;
           }  

       }

       return false;
    }

    public function getNumAndName(){

        $teachers = Teacher::join('user', 'user.user_no', '=', 'teacher.user_no')->get();

        $teacher_name_list = [];
        $teacher_id_list = [];

        foreach($teachers as $teacher){
            $teacher_name_list [] = $teacher->first_name . ' ' .  $teacher->last_name;
            $teacher_id_list [] = $teacher->user_no; 
        }

        return array($teacher_name_list, $teacher_id_list);

    }

    public function getTeacherInfo($id){
         $teacher = new Teacher;

         return $teacher->join('user', 'user.user_no', '=', 'teacher.user_no')->where('teacher.user_no', '=', $id)->first();

     }
}
