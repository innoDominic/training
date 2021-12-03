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

        $classes = new ClassesController;
        $attendance = new Attendance;
        
        $request = new Request;
        $date = '';
        if($request->has('selected_date')){
            $date = $request->get('selected_date');
        }else{
            $date = date('m/d/Y');
        }

        $result = $this->getStudentsAttendanceByDate($date);
        $selected_class = $classes->getClassInfo($class_no);

        return view('attendance-edit', [
            'result' => $result,
            'selected_class' => $selected_class,
            'selected_date' => $date
        ]);

    }

    public function store(Request $request){

    }

    public function update(Request $request){
        $count = $request->input('student_count');
        $user_no = session()->get('user_no');
        $selected_date = date('m/d/Y', strtotime($request->input('attendance_date')));

        $success = [];

        for($i = 0; $i < $count; $i++){

            # index 0 : checkbox checked or not / index 1 : user_no
            $checkboxValues = $request->input("attendance-" . $i);
            $plot_no = $request->input("plotted_class_no_" . $i);
            $attendance = new Attendance;
            $result = $attendance->where('plot_no', $plot_no)
            ->where('teacher_no', $user_no)
            ->where('att_date', $selected_date)
            ->first();

            if(!empty($result)){
                $attendance->where('plot_no', $plot_no)
                ->where('teacher_no', $user_no)
                ->where('att_date', $selected_date)
                ->update([
                    'att_status' => $checkboxValues
                ]);

                $success [] = true;
            }else{
                $attendance->plot_no = $plot_no;
                $attendance->att_status = $checkboxValues;
                $attendance->teacher_no = $user_no;
                $attendance->att_date = $selected_date;
                $attendance->save();

                $success [] = true;
            }

        }

        $success_count = count($success);

        if($success_count > 0 && $success_count == $count){
            return redirect()->route('attendance.edit', [
                'attendance' => $request->input('selected_class'),
                'selected_date' => $selected_date
            ]);
        }else{
            dd("One of the attendance was not correctly saved, please try again");
        }
    }

    public function destroy($user_no){
        
    }

    public function show(Request $request){

    }

    public function getStudentsAttendanceByDate($date){
        $attendance = new Attendance;

        return $attendance->select('user.first_name', 'user.last_name', 'student.student_id', 'plot_class.user_no', 'plot_class.plot_no', 'attendance.att_status')
        ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'attendance.plot_no')
        ->join('student', 'student.user_no', '=', 'plot_class.user_no')
        ->join('user', 'user.user_no', '=', 'student.user_no')
        ->where('attendance.att_date', $date)
        ->get();
    }
}
