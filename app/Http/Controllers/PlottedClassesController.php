<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PlottedClasses;

class PlottedClassesController extends Controller
{
    public function showStudentsByClass($class_no){
        
        $plotted_classes = new PlottedClasses;
        return $plotted_classes->select('user.first_name', 'user.last_name', 'student.student_id', 'plotted_classes.user_no')
            ->join('user', 'user.user_no', '=', 'plotted_classes.user_no')
            ->join('student', 'student.user_no', '=', 'user.user_no')
            ->where('user.user_type', 2)
            ->where('plotted_classes.classes_no', $class_no)->get();

    }
}
