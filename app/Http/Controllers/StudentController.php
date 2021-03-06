<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\PlottedClasses;
use App\Models\Attendance;

use App\Http\Controllers\PlottedClassesController;
use App\Http\Controllers\AttendanceController;

class StudentController extends Controller
{   

    public function create(){
        
        return view('student-create', [
            'result' => ''
        ]);

    }

    public function edit($user_no){
        
        return view('student-edit', [
            'result' => '',
            'student_info' => $this->getStudentInfo($user_no)
        ]);

    }

    public function store(Request $request){
     
        #Check if Request is empty
        $empty_field = $this->checkValuesIfEmpty($request);
        if( $empty_field !== null){
            return view('student-create', [
                'result' => 'Please fill up ' . $empty_field
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

    public function update(Request $request){
     
        #Check if Request is empty
        $empty_field = $this->checkValuesIfEmpty($request);
        if( $empty_field !== null){
            return view('student-edit', [
                'result' => 'Please fill up ' . $empty_field,
                'student_info' => $this->getStudentInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfUsernameExist($request->input('user_name'), $request->input('user_no'))){
            return view('student-edit', [
                'result' => 'Username Already exists',
                'student_info' => $this->getStudentInfo($request->input('user_no'))
            ]);
        }

        if($this->checkIfStudentIdExist($request->input('student_id'), $request->input('user_no'))){
            return view('student-edit', [
                'result' => 'Student ID Already exists',
                'student_info' => $this->getStudentInfo($request->input('user_no'))
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

        $student = new Student;

        $student->where('user_no', $request->input('user_no'))
        ->update([
            'student_id' => $request->input('student_id')
        ]);

        return view('student-edit', [
            'result' => 'Saved',
            'student_info' => $this->getStudentInfo($request->input('user_no'))
        ]);

    }

    public function destroy($user_no){
        $user            = new User;
        $student         = new Student;
        $plotted_classes = new PlottedClasses;
        $attendance      = new Attendance;

        $plotted_no   = $plotted_classes->select('plot_no')->where('user_no', $user_no)->get('plot_no')->toArray();
        $plotted_nums = [];

        foreach($plotted_no as $plot_no){
            $plotted_nums [] = $plot_no["plot_no"];
        }

        $user->where('user_no', '=', $user_no)->delete();
        $student->where('user_no', '=', $user_no)->delete();
        $attendance->whereIn('plot_no', $plotted_nums)->get()->each(function($att){
            $att->delete();
        });
        $plotted_classes->where('user_no', '=', $user_no)->delete();

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
    
            $user->user_name  = $csvValues[1];
            $user->password   = $csvValues[4];
            $user->first_name = $csvValues[2];
            $user->last_name  = $csvValues[3];
            $user->user_type  = 2;
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

    public function checkValuesIfEmpty(Request $request){
        $input_names = ['student_id', 'user_name', 'password', 'first_name', 'last_name'];

        foreach($input_names as $name){
            if( !$request->has($name) || empty($request->input($name)) ){
                return $name;
            }
        }

        return null;
    }

    public function show(Request $request){

        $student = new Student;

        if($request->has('srchStudentByName') && 
        $request->has('srchStudentByClass') && 
        $request->has('srchStudentByTeacher') ) {

            $selected_class   = $request->input('srchStudentByClass');
            $selected_teacher = $request->input('srchStudentByTeacher');
            $student_name     = trim($request->input('srchStudentByName'));

            #dd($student_name, $selected_class, $selected_teacher);

            $student = $student->select('student.user_no', 'student.student_id', 'user.first_name', 'user.last_name');

            #Query Build - Join
            $student = $student->join('user', 'user.user_no', '=', 'student.user_no');

            if(!empty($selected_teacher) && !empty($selected_class)){

                $student = $student->join('plotted_classes as plot_class', 'plot_class.user_no', '=', 'user.user_no')
                ->join('plotted_classes_teacher as plot_class_teach', 'plot_class_teach.student_plot_no', '=', 'plot_class.plot_no')
                ->join('classes', 'classes.classes_no', '=', 'plot_class.classes_no'); 
                 
            }else if(empty($selected_teacher) && !empty($selected_class)){

                $student = $student
                ->join('plotted_classes as plot_class', 'plot_class.user_no', '=', 'user.user_no')
                ->join('plotted_classes_teacher as plot_class_teach', 'plot_class_teach.student_plot_no', '=', 'plot_class.plot_no')
                    ->join('classes', 'classes.classes_no', '=', 'plot_class.classes_no');

            }else if(!empty($selected_teacher) && empty($selected_class)){

                $student = $student->join('plotted_classes as plot_class', 'plot_class.user_no', '=', 'user.user_no')
                    ->join('plotted_classes_teacher as plot_class_teach', 'plot_class_teach.student_plot_no', '=', 'plot_class.plot_no')
                    ->join('classes', 'classes.classes_no', '=', 'plot_class.classes_no');

            }

            #Query Build - Additional Selection
            if(!empty($selected_class)){
                $student = $student->addSelect('classes.classes_no', 'classes.classes_name', 'plot_class_teach.teacher_user_no');
            }

            if(!empty($selected_teacher)){
                $student = $student->addSelect('plot_class_teach.teacher_user_no', 'classes.classes_no', 'classes.classes_name');
            }

            #Query Build - Where Conditions
            if(!empty($selected_class) && !empty($selected_teacher)){

                $student = $student->where('plot_class_teach.teacher_user_no', $selected_teacher)
                ->where('classes.classes_no', $selected_class);

            }else if(!empty($selected_class) && empty($selected_teacher)){

                $student = $student->where('classes.classes_no', '=', $selected_class);

            }else{

                if(!empty($selected_class)){
                    $student = $student->where('classes.classes_no', '=', $selected_class);
                }

                if(empty($selected_class) && !empty($selected_teacher)){
                    $classes_list = PlottedClassesController::getPlottedClassesByTeacher($selected_teacher);
                    $classes_ids = [];
    
                    foreach($classes_list as $class){
                        $classes_ids [] = $class->classes_no;
                    }
    
                    $student = $student->whereIn('plot_class.classes_no', $classes_ids)
                    ->where('user.user_type', 2)
                    ->where('plot_class_teach.teacher_user_no', $selected_teacher);
                }

            }

            if(!empty($student_name)){
                $name = '%' . $student_name . '%';

                $student = $student->where(function ($student) use ($name) {
                    $student->where('user.first_name', 'like', $name)
                    ->orWhere('user.last_name', 'like', $name);
               }) ;
            }

            return $student->orderBy('student.student_id', 'asc')->paginate(3)->withPath('/admin/student');

        }

        return $student->select('student.user_no', 'student.student_id', 'user.first_name', 'user.last_name')
        ->join('user', 'user.user_no', '=', 'student.user_no')
        ->orderBy('student.student_id', 'asc')
        ->paginate(3)->withPath('/admin/student');

    }

    public function getStudentInfo($id){
        $student = new Student;

        return $student->join('user', 'user.user_no', '=', 'student.user_no')->where('student.user_no', '=', $id)->first();
    }

    public function getStudentNo($student_id){
        return Student::select('user_no')->where('student_id', $student_id)->first();
    }

    public function apiGetStudentInfo(Request $request){

        $student = new Student;

        $student_info = $student->join('user', 'user.user_no', '=', 'student.user_no')
        ->where('student.student_id', $request->input('student_id'))->get();

        return response(['Request' => $student_info]);

    }
}
