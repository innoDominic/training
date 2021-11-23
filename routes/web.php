<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassesController;

use App\Models\Student;
use App\Models\User;
use App\Models\Teacher;
use App\Models\PlottedClasses;

Route::get('/', function () {
    if(!session()->has('user_no')){

        return view('login', ['result' => '']);
    }else{
        $user_type = session('user_type');

        if($user_type == 0){

            return redirect('/admin/student');
        }else if($user_type == 1){

            return view('login', ['result' => 'teacher']);
        }
    }
});

Route::get('/logout', function () {

    Session::flush();
    return redirect('/');

});

Route::get('/admin/{page}', function ($page) {
    if(Session::has('user_type')){
        $user_type = Session::get('user_type');

        if($user_type == 0){

            if( Str::contains($page, 'student-create') ){

                return view('admin', [
                   'page' => $page,
                   'result' => ''
                ]);

            }else if( Str::contains($page, 'student-edit') ){

             return view('admin', [
                 'page' => 'student-edit',
                 'result' => '',
                 'student_info' => StudentController::getStudentInfo(request('id'))
             ]);

            }else{

                list($teacher_names, $teacher_ids) = TeacherController::getNumAndName();
                $teacher_count = count($teacher_names);
                $teacher_select_options = "";

                for($i = 0; $i < $teacher_count; $i++){
                    $teacher_select_options .= "
                        <option value='". $teacher_ids[$i] ."'>". $teacher_names[$i] ."</option>
                    ";
                }

                list($class_names, $class_ids) = ClassesController::getNumAndName();
                $class_count = count($teacher_names);
                $class_select_options = "";

                for($i = 0; $i < $class_count; $i++){
                    $class_select_options .= "
                        <option value='". $class_ids[$i] ."'>". $class_names[$i] ."</option>
                    ";
                }

                $result = '';

                if(request()->has('result')){
                    $result = request('result');
                }

                return StudentController::show(request(), $teacher_select_options, $class_select_options, $result);

            }
        }else if($user_type == 1){

            return redirect('/teacher');

        }else{

            return view('login', ['result' => '']);

        }
     }

     return redirect('/');
})->name('admin');

Route::get('/teacher/{page}', function ($page) {
    if(Session::has('user_type')){
       $user_type = Session::get('user_type');

       if($user_type == 0){
           
           return redirect('/admin/student');
   
       }else if($user_type == 1){
   
           return view('teacher', ['page' => $page]);
   
       }else{
   
           return redirect('/');
   
       }
    }

    return redirect('/');
});

Route::post('/admin/student/search', function(){
    list($teacher_names, $teacher_ids) = TeacherController::getNumAndName();
    $teacher_count = count($teacher_names);
    $teacher_select_options = "";

    for($i = 0; $i < $teacher_count; $i++){
        $teacher_select_options .= "
            <option value='". $teacher_ids[$i] ."'>". $teacher_names[$i] ."</option>
        ";
    }

    list($class_names, $class_ids) = ClassesController::getNumAndName();
    $class_count = count($teacher_names);
    $class_select_options = "";

    for($i = 0; $i < $class_count; $i++){
        $class_select_options .= "
            <option value='". $class_ids[$i] ."'>". $class_names[$i] ."</option>
        ";
    }

    $result = '';

    if(request()->has('result')){
        $result = request('result');
    }

    return StudentController::show(request(), $teacher_select_options, $class_select_options, $result);
});

Route::post('/', 'LoginController@authenticateUser');
Route::post('/admin/student-create', 'StudentController@create');
Route::post('/admin/student-edit', 'StudentController@edit');
Route::get('/admin/student/delete', 'StudentController@delete');
Route::post('/admin/student/csv', 'StudentController@createWithCSV');
Route::post('/admin/student/search', 'StudentController@show');


Route::get('/test', function () {

   $student = new Student;

   $student_list = $student->select('student.student_id', 'userA.first_name as student_first_name', 'userA.last_name as student_last_name', 'classes.classes_name', 'userB.first_name as teacher_first_name', 'userB.last_name as teacher_last_name')
   ->join('user as userA', 'userA.user_no', '=', 'student.user_no')
        ->leftJoin('plotted_classes as plot_classA', 'plot_classA.user_no', '=', 'student.user_no')
        ->join('classes', 'classes.classes_no', '=', 'plot_classA.classes_no')
        ->rightJoin('plotted_classes as plot_classB', 'plot_classB.classes_no', '=', 'plot_classA.classes_no')
        ->leftJoin('teacher', 'teacher.user_no', '=', 'plot_classB.user_no')
        ->leftJoin('user as userB', 'userB.user_no', '=', 'teacher.user_no')->paginate(3);

   return view('test', [
       'test' => $student_list
   ]);

})->name('test');