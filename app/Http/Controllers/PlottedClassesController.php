<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlottedClasses;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;

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

    public function getClassesByTeacher($user_no){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('classes.classes_name', 'classes.classes_no', 'period')
            ->join('classes', 'classes.classes_no', '=', 'plotted_classes.classes_no')
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
    
    public function deletePlottedClass(Request $request){
        
        $plot_class = new PlottedClasses;

        $plot_class->where('user_no', $request->input('id'))
        ->where('classes_no', $request->input('class'))
        ->where('period', $request->input('period'))->delete();

        return redirect()->route('plot-class-list', [
            'selected_class' => $request->input('class')
        ]);

    }

    public function deletePlottedTeacher(Request $request){
        
        $plot_class = new PlottedClasses;

        $plot_class->where('user_no', $request->input('id'))
        ->where('classes_no', $request->input('class'))
        ->where('period', $request->input('period'))->delete();

        return redirect()->route('plot-teacher-list', [
            'selected_teacher' => $request->input('id')
        ]);

 }
}
