<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

use Illuminate\Support\Facades\DB;

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

    public function update(Request $request){
        $count = $request->input('student_count');
        $user_no = session()->get('user_no');
        $selected_date = date('Y/m/d', strtotime($request->input('attendance_date')));

        $success = [];

        for($i = 0; $i < $count; $i++){

            $checkboxValue = $request->input("attendance-" . $i);
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
                    'att_status' => $checkboxValue
                ]);

                $success [] = true;
            }else{
                $attendance->plot_no    = $plot_no;
                $attendance->att_status = $checkboxValue;
                $attendance->teacher_no = $user_no;
                $attendance->att_date   = $selected_date;
                $attendance->save();

                $success [] = true;
            }

        }

        $success_count = count($success);

        if($success_count > 0 && $success_count == $count){
            $selected_date = urlencode(urlencode($selected_date));

            return redirect()->route('attendance.edit', [
                'class_no' => $request->input('selected_class'),
                'selected_date' => $selected_date
            ]);
        }else{
            dd("One of the attendance was not correctly saved, please try again");
        }
    }

    public function getAttendanceByDateAndClass($date, $class_no){
        $attendance = new Attendance;

        $check_attendance = $attendance->join('plotted_classes', 'plotted_classes.plot_no', '=', 'attendance.plot_no')
        ->where('attendance.att_date', $date)
        ->where('plotted_classes.classes_no', $class_no)
        ->get();

        if(count($check_attendance) > 0){

            return $attendance->select('user.first_name', 'user.last_name', 'student.student_id', 'plot_class.user_no', 'plot_class.plot_no', 'attendance.att_status')
            ->join('plotted_classes as plot_class', 'plot_class.plot_no', '=', 'attendance.plot_no')
            ->join('student', 'student.user_no', '=', 'plot_class.user_no')
            ->join('user', 'user.user_no', '=', 'student.user_no')
            ->where('attendance.att_date', $date)
            ->where('plot_class.classes_no', $class_no)
            ->get();

        }else{

           $plotted_classes = new PlottedClassesController;
           return $plotted_classes->getStudentsIncludedByClass($class_no);

        }
    }

    public function getClassNameAndAttendance(){
        $user_no = session()->get('user_no');
        $attendance = new Attendance;

        $query = $attendance->select('class.classes_name', 'class.classes_no', 'attendance.att_date', 'attendance.att_status')
        ->join('plotted_classes as p_class', 'p_class.plot_no', '=', 'attendance.plot_no')
        ->join('classes as class', 'class.classes_no', '=', 'p_class.classes_no')
        ->where('attendance.teacher_no', $user_no)
        ->orderBy('attendance.att_date', 'ASC')->get();

        return $query->groupBy('class.classes_no', 'attendance.att_date');
    }
}
