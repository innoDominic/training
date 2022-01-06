<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Teacher;
use App\Models\PlottedClasses;
use App\Models\Attendance;

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

    public function createWithCSV(Request $request){
        if($request->hasFile('csvFile')){

            $csvValues = explode(',',$request->file('csvFile')->get());
            $count = count($csvValues);

            for($i = 0; $i < $count; $i++){
                if(empty($csvValues[$i])){
      
                    return redirect()->route('teacher-list', [
                        'result' => 'One of the values is missing, please check the file'
                    ]);

                }
            }

            if($this->checkIfUsernameExist($csvValues[1])){
                return redirect()->route('teacher-list', [
                    'result' => 'Username Already exists'
                ]);
            }
    
            if($this->checkIfTeacherIdExist($csvValues[0])){
                return redirect()->route('teacher-list', [
                    'result' => 'Teacher ID Already exists'
                 ]);
            }
    
            $user = new User;
            $teacher = new Teacher;
    
            $user->user_name   =  $csvValues[1];
            $user->password    =  $csvValues[4];
            $user->first_name  =  $csvValues[2];
            $user->last_name   =  $csvValues[3];
            $user->user_type   =  1;
            $user->save();
    
            $teacher->teacher_id    = $csvValues[0];
            $teacher->teacher_title = $csvValues[5];
            $teacher->user_no       = $user->user_no;
            $teacher->save();

            return redirect()->route('teacher-list', [
                'result' => 'User Saved'
            ]);

        }else{
            
            return redirect()->route('teacher-list', [
                'result' => 'File does not exist'
            ]);

        }
    }

    public function edit(Request $request){
     
        #Check if Request is empty
        $empty_field = $this->checkValuesIfEmpty($request);
        if( $empty_field !== null){
            return view('teacher-edit', [
                'result'       => 'Please fill up ' . $empty_field,
                'teacher_info' => $this->getTeacherInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfUsernameExist($request->input('user_name'), $request->input('user_no'))){
            return view('teacher-edit', [
                'result' => 'Username Already exists',
                'teacher_info' => $this->getTeacherInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfTeacherIdExist($request->input('teacher_id'), $request->input('user_no'))){
            return view('teacher-edit', [
                'result' => 'Teacher ID Already exists',
                'teacher_info' => $this->getTeacherInfo($request->input('user_no'))
            ]);
        }

        $user = new User;

        $user->where('user_no', $request->input('user_no'))
        ->update([
            'user_name'  => $request->input('user_name'),
            'password'   => $request->input('password'),
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
        ]);

        $teacher = new Teacher;

        $teacher->where('user_no', $request->input('user_no'))
        ->update([
            'teacher_id'    => $request->input('teacher_id'),
            'teacher_title' => $request->input('title')
        ]);

        return view('teacher-edit', [
            'result'       => 'Saved',
            'teacher_info' => $this->getTeacherInfo($request->input('user_no'))
        ]);

    }

    public function show(Request $request){
        
        $teacher = new Teacher;

        if($request->has('srchTeacherByName') && $request->has('srchTeacherByClass')){

            $name  = trim($request->input('srchTeacherByName'));
            $class = $request->input('srchTeacherByClass');

            $teacher = $teacher->select('user.first_name', 'user.last_name', 'user.user_no', 'teacher.teacher_title', 'teacher.teacher_id');

            #Query Build - Additional Selection
            if(!empty($class)){
                $teacher = $teacher->addSelect('classes.classes_name', 'plot_class.period');
            }

            #Query Build - Join
            $teacher = $teacher->join('user', 'user.user_no', '=', 'teacher.user_no');

            if(!empty($class)){
                $teacher = $teacher->join('plotted_classes as plot_class', 'plot_class.user_no', '=', 'user.user_no')->join('classes', 'classes.classes_no', '=', 'plot_class.classes_no');
            }

            #Query Build - Where Conditions
            if(!empty($class)){
                $teacher = $teacher->where('classes.classes_no', $class);
            }

            if(!empty($name)){
                $teacher = $teacher->where(function ($teacher) use ($name) {
                    $teacher->where('user.first_name', 'like', '%' . $name . '%')
                    ->orWhere('user.last_name', 'like', '%' . $name . '%');
                });
            }

            return $teacher->paginate(3)->withPath('/admin/teacher');

        }

        return $teacher->select('user.first_name', 'user.last_name', 'user.user_no', 'teacher.teacher_title', 'teacher.teacher_id')->join('user', 'user.user_no', '=', 'teacher.user_no')->paginate(3)->withPath('/admin/teacher');

    }

    public function destroy($user_no){
        $user = new User;
        $teacher = new Teacher;
        $plotted_classes = new PlottedClasses;
        $attendance = new Attendance;

        $plotted_no = $plotted_classes->select('plot_no')->where('user_no', $user_no)->get('plot_no')->toArray();
        $plotted_nums = [];

        foreach($plotted_no as $plot_no){
            $plotted_nums [] = $plot_no["plot_no"];
        }

        $user->where('user_no', '=', $user_no)->delete();
        $teacher->where('user_no', '=', $user_no)->delete();
        $attendance->whereIn('plot_no', $plotted_nums)->get()->each(function($att){
            $att->delete();
        });
        $plotted_classes->where('user_no', '=', $user_no)->delete();

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

        return Teacher::join('user', 'user.user_no', '=', 'teacher.user_no')->get();

    }

    public function getTeacherInfo($id){
         $teacher = new Teacher;

         return $teacher->join('user', 'user.user_no', '=', 'teacher.user_no')->where('teacher.user_no', '=', $id)->first();

     }
}
