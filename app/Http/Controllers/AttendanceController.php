<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

use App\Http\Controllers\PlottedClassesController;
use App\Http\Controllers\ClassesController;

class AttendanceController extends Controller
{
    public function index(){
        $user_no = session()->get('user_no');

        $plotted_classes = new PlottedClassesController;
        $result = $plotted_classes->getClassesByTeacher($user_no);

        return view('attendance', ['result' => $result]);
    }

    public function create(){

    }

    public function edit($class_no){

        $plotted_classes = new PlottedClassesController;
        $classes = new ClassesController;
        $attendance = new Attendance;

        $result = $plotted_classes->getStudentsIncludedByClass($class_no);
        $selected_class = $classes->getClassInfo($class_no);

        $students_present = $attendance->select('plot_class.user_no')
        ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'attendance.plot_no')
        ->where('plot_class.classes_no', $class_no)
        ->where('attendance.att_status', 1)
        ->get();

        return view('attendance-edit', [
            'result' => $result,
            'students_present' => $students_present,
            'selected_class' => $selected_class
        ]);

    }

    public function store(Request $request){

    }

    public function update(Request $request){
        $count = $request->input('student_count');
        $user_no = session()->get('user_no');

        $success = [];

        for($i = 0; $i < $count; $i++){

            # index 0 : checkbox checked or not / index 1 : user_no
            $checkboxValues = $request->input("attendance-" . $i);
            $plot_no = $request->input("plotted_class_no_" . $i);
            $attendance = new Attendance;
            $result = $attendance->where('plot_no', $plot_no)
            ->where('teacher_no', $user_no)->first();

            if(!empty($result)){
                $attendance->where('plot_no', $plot_no)->update([
                    'att_status' => $checkboxValues
                ]);

                $success [] = true;
            }else{
                $attendance->plot_no = $plot_no;
                $attendance->att_status = $checkboxValues;
                $attendance->teacher_no = $user_no;
                $attendance->save();

                $success [] = true;
            }

        }

        $success_count = count($success);

        if($success_count > 0 && $success_count == $count){
            return redirect('teacher/attendance/' . $request->input('selected_class') . '/edit');
        }else{
            dd("One of the attendance was not correctly saved, please try again");
        }
    }

    public function destroy($user_no){
        
    }

    public function show(Request $request){

    }
}
