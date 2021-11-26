<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\PlottedClassesController;

Route::get('/', function () {

        return view('login', ['result' => '']);

})->name('login')->middleware('checkIfLogged');

Route::get('/logout', function () {

    Session::flush();
    return redirect('/');

});

Route::post('/', 'LoginController@authenticateUser');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function(){
    
    Route::get('/student/create', function () {

        return view('student-create', [
            'result' => ''
        ]);

    })->name('student-create');

    Route::get('/student/edit', function () {

         return view('student-edit', [
             'result' => '',
             'student_info' => StudentController::getStudentInfo(request('id'))
         ]);

    })->name('student-edit');

    Route::get('/student', function () {

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

    })->name('student-list');

    Route::post('/student', function(){

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

    Route::get('/teacher/create', function () {

        return view('teacher-create', [
            'result' => ''
        ]);

    })->name('teacher-create');

    Route::get('/teacher/edit', function () {

        return view('teacher-edit', [
            'result' => '',
            'teacher_info' => TeacherController::getTeacherInfo(request('id'))
        ]);


    })->name('teacher-edit');

    Route::get('/teacher', function () {

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

    })->name('teacher-list');

    Route::get('/class/create', function(){

        return view('class-create', [
            'result' => ''
        ]);

    })->name('class-create');

    Route::get('/class/edit', function(){

        return view('class-edit', [
            'result' => '',
            'class_info' => ClassesController::getClassInfo(request('id'))
        ]);

    })->name('class-edit');

    Route::get('/class', function(){
      
        return view('class', [
            'class_table_results' => ClassesController::show()
        ]);

    })->name('class-list');

    Route::get('/plot-class', function(){

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

    })->name('plot-class-list');

    Route::post('/plot-class', function(){

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

    Route::post('/student/create', 'StudentController@create');
    Route::post('/student/csv', 'StudentController@createWithCSV');
    Route::post('/student/edit', 'StudentController@edit');
    Route::get('/student/delete', 'StudentController@delete');
    Route::post('/student/search', 'StudentController@show');

    Route::post('/teacher/create', 'TeacherController@create');
    Route::post('/teacher/edit', 'TeacherController@edit');
    Route::get('/teacher/delete', 'TeacherController@delete');

    Route::post('/class/create', 'ClassesController@create');
    Route::post('/class/edit', 'ClassesController@edit');
    Route::get('/class/delete', 'ClassesController@delete');

    Route::post('/plot-class/plot-student', 'PlottedClassesController@plotStudent');
    Route::get('/plot-class/delete', 'PlottedClassesController@deletePlottedClass'); 

});

/*Route::get('/test', function () {

   return view('test', [
       'test' => $student_list
   ]);

})->name('test');*/