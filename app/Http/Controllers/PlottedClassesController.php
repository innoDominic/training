<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlottedClasses;
use App\Models\PlottedClassesTeacher;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;

class PlottedClassesController extends Controller
{
    public function getStudentsIncludedByClass($class_no, $period){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no', 'plotted_classes.plot_no', 'period')
            ->join('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->join('student', 'student.user_no', '=', 'user.user_no')
            ->where('plotted_classes.classes_no', $class_no)
            ->where('plotted_classes.period', $period)->get();

    }

    public function getStudentsIncludedByClassAndTeacher($class_no, $period){
        
        $plot_class_teach = new PlottedClassesTeacher;
        $teacher_user_no = session()->get('user_no');

        return $plot_class_teach->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no', 'plotted_classes.plot_no', 'period')
            ->join('plotted_classes', 'plotted_classes.plot_no', '=', 'plotted_classes_teacher.student_plot_no')
            ->join('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->join('student', 'student.user_no', '=', 'user.user_no')
            ->where('plotted_classes.classes_no', $class_no)
            ->where('plotted_classes.period', $period)
            ->where('plotted_classes_teacher.teacher_user_no', $teacher_user_no)->get();

    }

    public function getPeriods(){

        $plot_class = new PlottedClassesTeacher;

        return $plot_class->from('plotted_classes_teacher as plot_class_teach')
        ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'plot_class_teach.student_plot_no')
        ->join('user', 'user.user_no', '=', 'plot_class.user_no')
        ->join('classes as class', 'class.classes_no', '=', 'plot_class.classes_no')->get();

    }

    public function getClassesByTeacher($user_no){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('classes.classes_name', 'classes.classes_no', 'period', 'plot_no')
            ->join('classes', 'classes.classes_no', '=', 'plotted_classes.classes_no')
            ->where('plotted_classes.user_no', $user_no)
            ->get();

    }

    public function getPlottedClassesByTeacher($user_no){
        
        $plotted_classes = new PlottedClassesTeacher;

        return $plotted_classes->from('plotted_classes_teacher as p_class_teach')
        ->select('classes.classes_name', 'classes.classes_no', 'period', 'plot_no')
            ->join('plotted_classes as p_class', 'p_class.plot_no', '=', 'p_class_teach.teacher_plot_no')
            ->join('classes', 'classes.classes_no', '=', 'p_class.classes_no')
            ->where('p_class_teach.teacher_user_no', $user_no)
            ->get();

    }

    public function getPlottedClassByTeacher($user_no){
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('plot_no')
            ->where('plotted_classes.user_no', $user_no)
            ->get();
    }

    public function getClassesUnderStudents(){
       
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->from('plotted_classes as plot_class')
        ->select('class.classes_name', 'class.classes_no', 'student.user_no')
        ->join('classes as class', 'class.classes_no', '=', 'plot_class.classes_no')
        ->join('student', 'student.user_no', '=', 'plot_class.user_no')
        ->get();

    }

    public function getClassesUnderTeachers(){
       
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->from('plotted_classes as plot_class')
        ->select('class.classes_name', 'class.classes_no', 'teacher.user_no', 'user.first_name', 'user.last_name')
        ->join('classes as class', 'class.classes_no', '=', 'plot_class.classes_no')
        ->join('teacher', 'teacher.user_no', '=', 'plot_class.user_no')
        ->join('user', 'user.user_no', '=', 'teacher.user_no')
        ->get();

    }

    public function getStudentsExcludedIn(Array $included_students, $period){
        
        $student = new Student;
        $plotted_classes = new PlottedClasses;

        $students_with_periods =  $plotted_classes->select("plotted_classes.user_no")
        ->join("teacher", "teacher.user_no", "!=", "plotted_classes.user_no")
        ->where("period", $period)
        ->whereNotIn('plotted_classes.user_no', $included_students)
        ->get()->toArray();

        foreach($students_with_periods as $students){
            $included_students [] = $students["user_no"];
        }

        return $student->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id')
            ->join('user', 'user.user_no', '=', 'student.user_no')
            ->whereNotIn('user.user_no', $included_students)->get();

    }

    public function plotStudentToClass(Request $request){
        
        $plot_class = new PlottedClasses;

        $plot_class->classes_no = $request->input('selected_class_to_plot');
        $plot_class->period = $request->input('selected_period_to_plot');
        $plot_class->user_no = $request->input('user_no');
        $plot_class->save();

        return redirect()->route('plot-class-list', [
            'selected_class' => $request->input('selected_class_to_plot'),
            'selected_period' => $request->input('selected_period_to_plot')
        ]);

    }

    public function plotClassToTeacher(Request $request){
    
        $plot_class = new PlottedClasses;
        $class_no = $request->input('class_no');
        $teacher_user_no = $request->input('selected_teacher_to_plot');
        $period = $request->input('selected_period_to_plot');

        $period_taken = $plot_class->from("plotted_classes")
        ->where("user_no", $teacher_user_no)
        ->where("period", $period)->get()->toArray();

        if(count($period_taken) > 0){
   
            return redirect()->route('plot-teacher-list', [
                'selected_teacher' => $teacher_user_no,
                'selected_period' => $period,
                'result' => 'Period Taken'
            ]);

        }

        $plot_class->classes_no = $class_no;
        $plot_class->user_no = $teacher_user_no;
        $plot_class->period = $period;
        $plot_class->save();

        return redirect()->route('plot-teacher-list', [
            'selected_teacher' => $teacher_user_no,
            'selected_period' => $period,
            'result' => 'Success'
        ]);

    }
    
    public function deletePlottedClass($plot_no, $selected_class){
        
        $plot_class = new PlottedClasses;
        $attendance = new Attendance;

        $attendance->where('plot_no', $plot_no)->get()->each(function($att){
            $att->delete();
        });
        $plot_class->where('plot_no', $plot_no)->delete();

        return redirect()->back();

    }

    public function deletePlottedTeacher($class_no, $selected_teacher, $selected_period){
        
        $plot_class = new PlottedClasses;
        $attendance = new Attendance;

        $selected_plotted_class = $plot_class->select("plot_no")
        ->where('classes_no', $class_no)
        ->where('user_no', $selected_teacher)
        ->where('period', $selected_period)->first();

        $attendance->where('plot_no', $selected_plotted_class->plot_no)->get()->each(function($att){
            $att->delete();
        });

        $plot_class->where('plot_no', $selected_plotted_class->plot_no)->delete();

        return redirect()->back();

    }

    public function plotStudentToTeacherClass(Request $request){

        $student_no = explode('-', $request->input('student_no'));

        $plot_class_teach = new PlottedClassesTeacher;
        $result = $plot_class_teach->from('plotted_classes_teacher as plot_class_teach')
        ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'plot_class_teach.student_plot_no')
        ->where('plot_class.period', $request->input('selected_period'))
        ->where('plot_class.classes_no', $request->input('selected_class'))
        ->where('plot_class.user_no', $student_no[1])->first();

        if($result == null){

            $plot_class_teach = new PlottedClassesTeacher;
            $plot_class_teach->student_plot_no = $student_no[0];
            $plot_class_teach->teacher_plot_no = $request->input('teacher_plot_no');
            $plot_class_teach->teacher_user_no = $request->input('teacher_user_no');
            $plot_class_teach->save();

            return redirect()->route('plot-student-teacher-list', [
                'teacher_plot_no' => $request->input('teacher_plot_no'),
                'class_name' => $request->input('selected_class_name'),
                'class_no' => $request->input('selected_class'),
                'period' => $request->input('selected_period'),
                'selected_teacher' => $request->input('teacher_user_no'),
                'result' => 'Success'
            ]);

        }
        
        return redirect()->route('plot-student-teacher-list', [
            'teacher_plot_no' => $request->input('teacher_plot_no'),
            'class_name' => $request->input('selected_class_name'),
            'class_no' => $request->input('selected_class'),
            'period' => $request->input('selected_period'),
            'selected_teacher' => $request->input('teacher_user_no'),
            'result' => 'Student is already assigned to a teacher'
        ]);

    }

    public function deletePlottedStudentTeacher($student_plot_no, $teacher_plot_no){
        
        $plot_class = new PlottedClassesTeacher;
        $plot_class->where('student_plot_no', $student_plot_no)->where('teacher_plot_no', $teacher_plot_no)->delete();

        return redirect()->back();

    }
}
