<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\PlottedClassesController;

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

        $teacher_options = TeacherController::getNumAndName();
        $class_options = ClassesController::getNumAndName();

        $result = '';

        if(request()->has('result')){
            $result = request('result');
        }

        return view('student', [
            'result' => $result,
            'teacher_options' => $teacher_options,
            'class_options' => $class_options,
            'student_table_results' => StudentController::show(request())
         ]);

    }

})->name('student-list');

Route::post('/admin/student', function(){
 
    $teacher_options = TeacherController::getNumAndName();

    $class_options = ClassesController::getNumAndName();

    $result = '';

    if(request()->has('result')){
        $result = request('result');
    }

    return view('student', [
        'result' => $result,
        'teacher_options' => $teacher_options,
        'class_options' => $class_options,
        'student_table_results' => StudentController::show(request())
     ]);

});

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

        $class_options = ClassesController::getNumAndName();

        $result = '';

        if(request()->has('result')){
            $result = request('result');
        }

        return view('teacher', [
            'result' => $result,
            'class_options' => $class_options,
            'teacher_table_results' => TeacherController::show(request())
        ]);

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

})->name('class-create');

Route::get('/admin/class/edit', function(){
    
    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
            'result' => ''
        ]);

    }else{

        return view('class-edit', [
            'result' => '',
            'class_info' => ClassesController::getClassInfo(request('id'))
        ]);

    }

})->name('class-edit');

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

Route::get('/admin/plot-class', function(){
    
    if(!session()->has('user_no') && !session()->get('user_type') == 0){

        return redirect()->route('login', [
            'result' => ''
        ]);

    }else{

        $class_options = ClassesController::getNumAndName();

        $selected_class = $class_options[0]->classes_no;
        if(request()->has('selected_class')){
            $selected_class = request('selected_class');
        }

        $included_students = PlottedClassesController::getStudentsIncludedInClass($selected_class);

        $included_students_id = [];
        foreach($included_students as $student){
            $included_students_id [] = $student->user_no;
        }

        return view('plot-class', [
            'class_options' => $class_options,
            'selected_class' => $selected_class,
            'student_options' => PlottedClassesController::getStudentsExcludedInClass($included_students_id),
            'student_table_results' => $included_students
        ]);

    }

})->name('plot-class-list');

Route::post('/admin/plot-class', function(){

    $class_options = ClassesController::getNumAndName();

    $included_students = PlottedClassesController::getStudentsIncludedInClass(request('selected_class'));

    $included_students_id = [];
    foreach($included_students as $student){
        $included_students_id [] = $student->user_no;
    }

    return view('plot-class', [
        'class_options' => $class_options,
        'selected_class' => request('selected_class'),
        'student_options' => PlottedClassesController::getStudentsExcludedInClass($included_students_id),
        'student_table_results' => $included_students
    ]);

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
Route::post('/admin/class/edit', 'ClassesController@edit');
Route::get('/admin/class/delete', 'ClassesController@delete');

Route::post('/admin/plot-class/plot-student', 'PlottedClassesController@plotStudent');

/*Route::get('/test', function () {

   return view('test', [
       'test' => $student_list
   ]);

})->name('test');*/