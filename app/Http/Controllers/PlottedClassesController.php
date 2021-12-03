<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlottedClasses;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Teacher;

class PlottedClassesController extends Controller
{
    public function getStudentsIncludedByClass($class_no){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no', 'plotted_classes.plot_no')
            ->join('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->join('student', 'student.user_no', '=', 'user.user_no')
            ->where('plotted_classes.classes_no', $class_no)->get();

    }

    public function getClassesByTeacher($user_no){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('classes.classes_name', 'classes.classes_no')
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

    public function getClassesExludedIn(Array $included_classes, $teacher_user_no){

        $class = new Classes;

        $plotted_classes = new PlottedClasses;
        $classes_with_teachers = $plotted_classes->from('plotted_classes as plot_class')
        ->select('plot_class.classes_no')
        ->groupBy('plot_class.classes_no')
        ->join('teacher', 'teacher.user_no', '=', 'plot_class.user_no')
        ->where('plot_class.user_no', '!=', $teacher_user_no)
        ->get()->toArray();

        return $class->select('classes_name', 'classes_no')
        ->whereNotIn('classes_no', $included_classes)
        ->whereNotIn('classes_no', $classes_with_teachers)
        ->get();

    }

    public function getStudentsExcludedIn(Array $included_students){
        
        $student = new Student;

        return $student->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id')
            ->join('user', 'user.user_no', '=', 'student.user_no')
            ->whereNotIn('user.user_no', $included_students)->get();

    }

    public function plotStudentToClass(Request $request){
        
        $plot_class = new PlottedClasses;

        $plot_class->classes_no = $request->input('selected_class_to_plot');
        $plot_class->user_no = $request->input('user_no');
        $plot_class->save();

        return redirect()->route('plot-class-list', [
            'selected_class' => $request->input('selected_class_to_plot')
        ]);

    }

    public function plotClassToTeacher(Request $request){
    
        $plot_class = new PlottedClasses;

        $plot_class->classes_no = $request->input('class_no');
        $plot_class->user_no = $request->input('selected_teacher_to_plot');
        $plot_class->save();

        return redirect()->route('plot-teacher-list', [
            'selected_teacher' => $request->input('selected_teacher_to_plot')
        ]);

    }
    
    public function deletePlottedClass(Request $request){
        
        $plot_class = new PlottedClasses;

        $plot_class->where('user_no', $request->input('id'))
        ->where('classes_no', $request->input('class'))->delete();

        return redirect()->route('plot-class-list', [
            'selected_class' => $request->input('class')
        ]);

    }

    public function deletePlottedTeacher(Request $request){
        
     $plot_class = new PlottedClasses;

     $plot_class->where('user_no', $request->input('id'))
     ->where('classes_no', $request->input('class'))->delete();

     return redirect()->route('plot-teacher-list', [
         'selected_teacher' => $request->input('id')
     ]);

 }
}
