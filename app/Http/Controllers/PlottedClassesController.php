<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlottedClasses;
use App\Models\Student;

class PlottedClassesController extends Controller
{
    public function getStudentsIncludedInClass($class_no){
        
        $plotted_classes = new PlottedClasses;

        return $plotted_classes->select('user.user_no', 'user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no')
            ->join('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->join('student', 'student.user_no', '=', 'user.user_no')
            ->where('plotted_classes.classes_no', $class_no)->get();

    }

    public function getStudentsExcludedInClass($included_students){
        
        $plotted_classes = new PlottedClasses;
        $student = new Student;

        $student_list = $student->select('user.first_name', 'user.last_name', 'student.student_id')
            ->join('user', 'user.user_no', '=', 'student.user_no')
            ->whereNotIn('user.user_no', $included_students)->get();

        /*$student_list = $plotted_classes->select('user.first_name', 'user.last_name', 'student.student_id')
            ->leftJoin('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->leftJoin('student', 'student.user_no', '=', 'user.user_no')
            ->where('user.user_type', 2)
            ->where('plotted_classes.classes_no', '!=', $class_no)->get();*/

        $student_name_list = [];
        $student_id_list = [];

        foreach($student_list as $student){
            $student_name_list [] = '(' . $student->student_id . ') ' . $student->last_name . ', ' .  $student->first_name;
            $student_id_list [] = $student->user_no; 
        }

        return array($student_name_list, $student_id_list);

    }
}
