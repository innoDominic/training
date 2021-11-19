<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function getNumAndName(){

        $teachers = Teacher::join('user', 'user.user_no', '=', 'teacher.user_no')->get();

        $teacher_name_list = [];
        $teacher_id_list = [];

        foreach($teachers as $teacher){
            $teacher_name_list [] = $teacher->first_name . ' ' .  $teacher->last_name;
            $teacher_id_list [] = $teacher->user_no; 
        }

        return array($teacher_name_list, $teacher_id_list);

    }
}
