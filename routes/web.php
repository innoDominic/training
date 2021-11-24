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

            return redirect()->route('student-list', [
                'result' => ''
            ]);

        }else if($user_type == 1){

            return redirect()->route('teacher-list', [
                'result' => ''
            ]);

        }
    }
})->name('login');

Route::get('/logout', function () {

    Session::flush();
    return redirect('/');

});

Route::get('/admin/student/create', function () {

    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
            'result' => ''
        ]);

    }else{

        return view('student-create', [
            'result' => ''
        ]);

    }

})->name('student-create');

Route::get('/admin/student/edit', function () {

    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
            'result' => ''
        ]);

    }else{

        return view('student-edit', [
            'result' => '',
            'student_info' => StudentController::getStudentInfo(request('id'))
        ]);

     }
   
})->name('student-edit');

Route::get('/admin/student', function () {

    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
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
        $class_count = count($class_names);
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

})->name('student-list');

Route::get('/admin/teacher/create', function () {

 if(!session()->has('user_no') && !session()->get('user_type') == 0){

     return redirect()->route('login', [
         'result' => ''
     ]);

 }else{

     return view('teacher-create', [
         'result' => ''
     ]);

 }

})->name('teacher-create');

Route::get('/admin/teacher/edit', function () {

 if(!session()->has('user_no') && !session()->get('user_type') == 0){

     return redirect()->route('login', [
         'result' => ''
     ]);

 }else{

     return view('teacher-edit', [
         'result' => '',
         'teacher_info' => TeacherController::getTeacherInfo(request('id'))
     ]);

  }

})->name('teacher-edit');

Route::get('/admin/teacher', function () {

    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
          'result' => ''
      ]);

    }else{

        list($class_names, $class_ids) = ClassesController::getNumAndName();
        $class_count = count($class_names);
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

        return TeacherController::show(request(), $class_select_options, $result);

    }

})->name('teacher-list');

Route::get('/admin/class/create', function(){
    
    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
            'result' => ''
        ]);

    }else{

        return view('class-create', [
            'result' => ''
        ]);

    }

});

Route::get('/admin/class', function(){

    if(!session()->has('user_no') && !session()->get('user_type') == 0){

         return redirect()->route('login', [
             'result' => ''
         ]);

    }else{
     
        return view('class', [
            'class_table_results' => ClassesController::show()
        ]);

    }

})->name('class-list');

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
Route::post('/admin/student/create', 'StudentController@create');
Route::post('/admin/student/csv', 'StudentController@createWithCSV');
Route::post('/admin/student/edit', 'StudentController@edit');
Route::get('/admin/student/delete', 'StudentController@delete');
Route::post('/admin/student/search', 'StudentController@show');

Route::post('/admin/teacher/create', 'TeacherController@create');
Route::post('/admin/teacher/edit', 'TeacherController@edit');
Route::get('/admin/teacher/delete', 'TeacherController@delete');

Route::post('/admin/class/create', 'ClassesController@create');

/*Route::get('/test', function () {

   return view('test', [
       'test' => $student_list
   ]);

})->name('test');*/