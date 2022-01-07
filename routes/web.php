<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\PlottedClassesController;
use App\Http\Controllers\AttendanceController;

use App\Models\PlottedClasses;
use App\Models\PlottedClassesTeacher;
use App\Models\Attendance;

Route::get('/', function () {

   if(session()->has('user_no')){
       if(session()->get('user_type') === 0){
           return redirect('admin/student');
       }else if(session()->get('user_type') === 1){
           return redirect('teacher/attendance');
       }else{
           dd("Unkown User");
       }
   }   

   return view('login', ['result' => '']);

})->name('login');

Route::get('/logout', function () {

    session()->flush();
    return redirect('/');

});

Route::post('/', 'LoginController@authenticateUser');

Route::group(['middleware' => 'redirect.authenticated'], function(){

    Route::resource('admin/student', 'StudentController')->except([
        'index', 'show'
    ]);

    Route::get('admin/student', function () {

        $teacher_options = TeacherController::getNumAndName();
        $class_options   = ClassesController::getNumAndName();

        $result = '';

        if(request()->has('result')){
            $result = request('result');
        }

        $student_to_search  = request('srchStudentByName');
        $selected_class     = request('srchStudentByClass');
        $selected_teacher   = request('srchStudentByTeacher');

        $teacher_name = '';
        foreach($teacher_options as $teacher){
            if($teacher->user_no == $selected_teacher){
                $teacher_name = $teacher->last_name . ' ' . $teacher->first_name;
            }
        }

        $class_name = '';
        foreach($class_options as $class){
            if($class->classes_no == $selected_class){
                $class_name = $class->classes_name;
            }
        }

        $periods = PlottedClassesController::getPeriods();

        return view('student', [
            'result'                  =>  $result,
            'teacher_options'         =>  $teacher_options,
            'class_options'           =>  $class_options,
            'selected_teacher'        =>  $selected_teacher,
            'selected_teacher_name'   =>  $teacher_name,
            'selected_class_name'     =>  $class_name,
            'selected_class'          =>  $selected_class,
            'student_to_search'       =>  $student_to_search,
            'periods'                 =>  $periods,
            'student_table_results'   =>  StudentController::show(request())
        ]);

    })->name('student-list');

    Route::post('admin/student/csv', 'StudentController@createWithCSV');

    Route::get('admin/teacher/create', function () {

        return view('teacher-create', [
            'result' => ''
        ]);

    })->name('teacher-create');

    Route::get('admin/teacher/edit', function () {

        return view('teacher-edit', [
            'result' => '',
            'teacher_info' => TeacherController::getTeacherInfo(request('id'))
        ]);


    })->name('teacher-edit');

    Route::get('admin/teacher', function () {

        $class_options = ClassesController::getNumAndName();

        $result = '';

        if(request()->has('result')){
            $result = request('result');
        }

        $teacher_to_search = request('srchTeacherByName');
        $selected_class    = request('srchTeacherByClass');

        $teacher_table_result = TeacherController::show(request());
        $included_classes_under_teacher = [];
        
        if(empty($selected_class)){
            foreach($teacher_table_result as $teacher){
                
                $included_classes = PlottedClassesController::getClassesByTeacher($teacher->user_no);

               
                foreach($included_classes as $class){
                    $included_classes_under_teacher ["teacher_no"][] = $teacher->user_no;
                    $included_classes_under_teacher ["class_name"][] = $class->classes_name;
                }

            }
        }

        return view('teacher', [
            'result'                         => $result,
            'class_options'                  => $class_options,
            'teacher_to_search'              => $teacher_to_search,
            'selected_class'                 => $selected_class,
            'included_classes_under_teacher' => $included_classes_under_teacher,
            'teacher_table_results'          => $teacher_table_result
        ]);

    })->name('teacher-list');

    Route::get('admin/class/create', function(){

        return view('class-create', [
            'result' => ''
        ]);

    })->name('class-create');

    Route::get('admin/class/edit', function(){

        return view('class-edit', [
            'result'     => '',
            'class_info' => ClassesController::getClassInfo(request('id'))
        ]);

    })->name('class-edit');

    Route::get('admin/class', function(){
      
        return view('class', [
            'class_table_results' => ClassesController::show()
        ]);

    })->name('class-list');

    Route::get('admin/plot-class', function(){

        $class_options = ClassesController::getNumAndName();

        $selected_class = $class_options[0]->classes_no;
        if(request()->has('selected_class')){
            $selected_class = request('selected_class');
        }

        $selected_period = "08:00 AM - 09:00 AM";
        if(request()->has('selected_period')){
            $selected_period = request('selected_period');
        }

        $included_students = PlottedClassesController::getStudentsIncludedByClass($selected_class, $selected_period);

        $included_students_id = [];
        foreach($included_students as $student){
            $included_students_id [] = $student->user_no;
        }

        return view('plot-class', [
            'class_options' => $class_options,
            'selected_class' => $selected_class,
            'selected_period' => $selected_period,
            'student_options' => PlottedClassesController::getStudentsExcludedIn($included_students_id, $selected_period),
            'student_table_results' => $included_students
        ]);

    })->name('plot-class-list');

    Route::post('admin/plot-class', function(){

        $class_options = ClassesController::getNumAndName();
        $selected_class = request('selected_class');
        $selected_period = request('selected_period');

        $included_students = PlottedClassesController::getStudentsIncludedByClass($selected_class, $selected_period);

        $included_students_id = [];
        foreach($included_students as $student){
            $included_students_id [] = $student->user_no;
        }

        return view('plot-class', [
            'class_options'   => $class_options,
            'selected_class'  => $selected_class,
            'selected_period' => $selected_period,
            'student_options' => PlottedClassesController::getStudentsExcludedIn($included_students_id, $selected_period),
            'student_table_results' => $included_students
        ]);

    });

    Route::get('admin/plot-teacher', function(){

        $teacher_options = TeacherController::getNumAndName();
        $selected_teacher = '';

        if(count($teacher_options) > 0){
            $selected_teacher = $teacher_options[0]->user_no;
        }

        if(request()->has('selected_teacher')){
            $selected_teacher = request('selected_teacher');
        }

        $selected_period = "08:00 AM - 09:00 AM";

        if(request()->has('selected_period')){
            $selected_period = request('selected_period');
        }

        $included_classes = PlottedClassesController::getClassesByTeacher($selected_teacher);

        $included_classes_id = [];
        foreach($included_classes as $class){
            $included_classes_id [] = $class->classes_no;
        }

        $result = '';
        if(request()->has('result')){
            $result = request('result');
        }

        return view('plot-teacher', [
            'teacher_options'     => $teacher_options,
            'selected_teacher'    => $selected_teacher,
            'selected_period'    => $selected_period,
            'class_options'       => ClassesController::show(),
            'class_table_results' => $included_classes,
            'result' => $result
        ]);

    })->name('plot-teacher-list');

    Route::post('admin/plot-teacher', function(){

        $teacher_options = TeacherController::getNumAndName();
        $class_options   = ClassesController::getNumAndName();

        $included_classes = PlottedClassesController::getClassesByTeacher(request('selected_teacher'));

        $included_classes_id = [];
        foreach($included_classes as $class){
            $included_classes_id [] = $class->classes_no;
        }

        $selected_period = "08:00 AM - 09:00 AM";

        if(request()->has('selected_period')){
            $selected_period = request('selected_period');
        }

        $result = '';
        if(request()->has('result')){
            $result = request('result');
        }

        return view('plot-teacher', [
            'teacher_options'     => $teacher_options,
            'selected_teacher'    => request('selected_teacher'),
            'selected_period'     => $selected_period,
            'class_options'       => ClassesController::show(),
            'class_table_results' => $included_classes,
            'result' => $result
        ]);

    });

    Route::post('admin/teacher/create', 'TeacherController@create');
    Route::post('admin/teacher/edit', 'TeacherController@edit');
    Route::post('admin/teacher/create/csv', 'TeacherController@createWithCSV');
    Route::delete('admin/teacher/delete/{user_no}', 'TeacherController@destroy')->name('teacher.destroy');

    Route::post('admin/class/create', 'ClassesController@create');
    Route::post('admin/class/edit', 'ClassesController@edit');
    Route::delete('admin/class/delete/{class_no}', 'ClassesController@destroy')->name('class.destroy');

    Route::post('admin/plot-class/plot-class-student', 'PlottedClassesController@plotStudentToClass');
    Route::post('admin/plot-teacher/plot-class-teacher', 'PlottedClassesController@plotClassToTeacher');
    Route::post('admin/plot-teacher/plot-class-teacher-student', 'PlottedClassesController@plotStudentToTeacherClass');

    Route::get('/admin/plot-teacher/plot-periods/{teacher_plot_no}/{class_name}/{class_no}/{period}/{selected_teacher}/edit', function($teacher_plot_no, $class_name, $class_no, $period, $selected_teacher){

        $plotted_students_teacher = new PlottedClassesTeacher;
        $plotted_students_teacher = $plotted_students_teacher->select('student_id', 'first_name', 'last_name', 'student_plot_no', 'teacher_user_no')
        ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'plotted_classes_teacher.student_plot_no')
        ->join('user', 'user.user_no', '=', 'plot_class.user_no')
        ->join('student', 'student.user_no', '=', 'user.user_no')
        ->where('plot_class.period', $period)
        ->where('plot_class.classes_no', $class_no)
        ->where('plotted_classes_teacher.teacher_plot_no', $teacher_plot_no)
        ->where('plotted_classes_teacher.teacher_user_no', $selected_teacher)->get();

        $student_plot_nums = [];
        foreach($plotted_students_teacher as $plot_class){
            $student_plot_nums [] = $plot_class->student_plot_no;
        }

        $plotted_classes = new PlottedClasses;
        $plotted_classes = $plotted_classes->join('classes as class', 'class.classes_no', '=', 'plotted_classes.classes_no')
        ->join('student', 'student.user_no', '=', 'plotted_classes.user_no')
        ->join('user', 'user.user_no', '=', 'student.user_no')
        ->where('plotted_classes.classes_no', $class_no)
        ->whereNotIn('plotted_classes.plot_no', $student_plot_nums)
        ->where('period', $period)->get();

        $result = '';
        if(request()->has('result')){
            $result = request('result');
        }
        
        return view('plot-teacher-students', [
            'students_unassigned' => $plotted_classes,
            'students_assigned' => $plotted_students_teacher,
            'selected_class' => $class_no,
            'selected_class_name' => $class_name,
            'selected_period' => $period,
            'selected_teacher' => $selected_teacher,
            'teacher_plot_no' => $teacher_plot_no,
            'result' => $result
        ]);

    })->name('plot-student-teacher-list');

    Route::delete('/admin/plot-teacher/plot-class-teacher-student/{student_plot_no}/{teacher_plot_no}', 'PlottedClassesController@deletePlottedStudentTeacher')->name('plot-class-teacher-student.destroy');

    Route::delete('admin/plot-class/delete/{plot_no}/{selected_class}', 'PlottedClassesController@deletePlottedClass')->name('plot_class.destroy');
    Route::delete('admin/plot-teacher/delete/{class_no}/{selected_teacher}/{period}', 'PlottedClassesController@deletePlottedTeacher')->name('plot_class_teacher.destroy');

    Route::get('teacher/attendance/{class_no}/{selected_date}/{period}/edit', function($class_no, $selected_date, $period){

        $classes    = new ClassesController;
        $attendance = new AttendanceController;
        
        $date   = date('Y/m/d', strtotime(urldecode($selected_date)));
        $period = urldecode($period);

        list($students, $attendance) = $attendance->getAttendanceByDatePeriodAndClass($date, $period, $class_no);
        $selected_class              = $classes->getClassInfo($class_no);

        return view('attendance-edit', [
            'students'        => $students,
            'attendance'      => $attendance,
            'selected_class'  => $selected_class,
            'selected_date'   => $date,
            'selected_period' => $period
        ]);
    })->name('attendance.edit');

    Route::get('/teacher/attendance/report/csv', 'AttendanceController@downloadCSV');

    Route::resource('teacher/attendance', 'AttendanceController')->except(['edit']);

});