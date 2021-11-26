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

        return $plotted_classes->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no')
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

    public function getClassesExludedIn(Array $included_classes){

        $class = new Classes;

        return $class->select('classes_name', 'classes_no')
        ->whereNotIn('classes_no', $included_classes)->get();

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
