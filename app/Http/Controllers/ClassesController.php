<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Classes;
use App\Models\PlottedClasses;
use App\Models\Attendance;

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

    public function edit(Request $request){
        
        if(!$request->has('classes_name') || empty($request->input('classes_name'))){
               
            return view('class-edit', [
                'result' => 'Please fill up class name',
                'class_info' => $this->getClassInfo($request->input('classes_no'))
            ]);

        }

        if($this->checkIfClassNameExist($request->input('classes_name'), $request->input('classes_no'))){

            return view('class-edit', [
                'result' => 'Class name already exists',
                'class_info' => $this->getClassInfo($request->input('classes_no'))
            ]);

        }

        $class = new Classes;
        $class->where('classes_no', '=', $request->input('classes_no'))
            ->update([
                'classes_name' => $request->input('classes_name')
            ]);
      
        return view('class-edit', [
            'result' => 'Class Name Updated',
            'class_info' => $this->getClassInfo($request->input('classes_no'))
        ]);

    }

    public function destroy($class_no){
        $class = new Classes;
        $plotted_classes = new PlottedClasses;
        $attendance = new Attendance;

        $plotted_no = $plotted_classes->select('plot_no')->where('classes_no', $class_no)->get('plot_no')->toArray();
        $plotted_nums = [];

        foreach($plotted_no as $plot_no){
            $plotted_nums [] = $plot_no["plot_no"];
        }

        $attendance->whereIn('plot_no', $plotted_nums)->get()->each(function($att){
            $att->delete();
        });
        $plotted_classes->where('classes_no', $class_no)->get()->each(function($plotted_class){
            $plotted_class->delete();
        });

        $class->where('classes_no', '=', $class_no)->delete();

        return redirect()->route('class-list');
    }

    public function getNumAndName(){

        return Classes::select('classes_no', 'classes_name')->orderBy('classes_no')->get();

    }

    public function checkIfClassNameExist($className, $id = null){

        $class = new Classes;

        if($id === null){

            $get_duplicates = $class->where('classes_name', '=', $className)->get();

            if($get_duplicates->count() > 0){
                return true;
            }

        }else{

            $get_duplicates = $class->where('classes_name', '=', $className)->where('classes_no', '!=', $id)->get();

            if($get_duplicates->count() > 0){
                return true;
            }

        }

        return false;

    }

    public function getClassInfo($id){
        $class = new Classes;

        return $class->where('classes_no', $id)->first();
    }
}
