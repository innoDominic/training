<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classes;

class ClassesController extends Controller
{
    
    public function show(){
        
        $class = new Classes;

        return $class->select('classes_name', 'classes_no')->get();

    }

    public function create(Request $request){

        if(!$request->has('class_name') || empty($request->input('class_name'))){
            
            return view('class-create', [
                'result' => 'Please fill up class name'
            ]);

        }

        if($this->checkIfClassNameExist($request->input('class_name'))){
   
            return view('class-create', [
                'result' => 'Class name already exists'
            ]);

        }

        $class = new Classes;

        $class->classes_name = $request->input('class_name');
        $class->save();

        return view('class-create', [
            'result' => 'Class saved'
        ]);

    }

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

    public function checkIfClassNameExist($className, $id = null){

        $class = new Classes;

        if($id === null){

            $get_duplicates = $class->where('classes_name', '=', $className)->get();

            if($get_duplicates->count() > 0){
                return true;
            }

        }else{

            $get_duplicates = $class->where('classes_name', '=', $className)->where('classes_no', '!=', $id)->first();

            if($get_duplicates->count() > 0){
                return true;
            }

        }

        return false;

    }
}
