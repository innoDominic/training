<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassesController;

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

            if( Str::contains($page, '-create') ){

                return view('admin', [
                   'page' => $page,
                   'result' => ''
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

                $output = '';

                if(request()->has('result')){
                    $output = request('result');
                }

                return view('admin', [
                    'page' => $page,
                    'result' => $output,
                    'teacher_options' => $teacher_select_options,
                    'class_options' => $class_select_options
                ]);

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

Route::post('/', 'LoginController@authenticateUser');
Route::post('/admin/student-create', 'StudentController@create');
Route::post('/admin/student/csv', 'StudentController@createWithCSV');


/*Route::get('/test', function () {
    ClassesController::getNumAndName();
    /*$class_count = count($teacher_names);
    $class_select_options = "";

    for($i = 0; $i < $class_count; $i++){
        $class_select_options .= "
            <option value='". $class_ids[$i] ."'>". $class_names[$i] ."</option>
        ";
    }
});*/