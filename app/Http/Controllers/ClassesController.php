<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;

class ClassesController extends Controller
{
    public function getNumAndName(){

        $classes = Classes::all();

        $class_name_list = [];
        $class_id_list = [];

        foreach($classes as $class){
            $class_name_list [] = $class->classes_name;
            $class_id_list [] = $class->classes_no; 
        }

        return array($class_name_list, $class_id_list);

    }
}
