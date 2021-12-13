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

    public function show(){
        list($class_dates, $classes, $classes_and_values) = $this->getClassNamesDatesAndAverages();

        return view('attendance-reports', [
            'classes'   => $classes,
            'dates'     => $class_dates,
            'averages'  => $classes_and_values
        ]);
    }

    public function downloadCSV(){
        $fileName = 'Report.csv';
        list($class_dates, $classes, $classes_and_values) = $this->getClassNamesDatesAndAverages();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Class');
        foreach($class_dates as $date){
            $columns [] = $date;
        }

        $callback = function() use($classes_and_values, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($classes_and_values as $class_key => $class_value){
                $row_manager = [];
                $row['Class']  = $class_key;
                $row_manager [] = $row['Class'];
                foreach($class_value as $date_key => $date_value){
                    $row[$date_key] = $date_value['average'];
                    $row_manager[] = $row[$date_key];
                }
                fputcsv($file, $row_manager);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    public function getClassNamesDatesAndAverages(){
        $results = $this->getClassNameAndAttendance();

        $classes_and_values = [];
        $classes = [];
        $class_dates = [];

        foreach($results as $value){
            foreach($value as $v){

                $date = date('Y/m', strtotime($v->att_date));

                $class_dates [] = $date;
                $classes [] = $v->classes_name;

                if(empty($classes_and_values[$v->classes_name][$date]['present'])){
                    $classes_and_values[$v->classes_name][$date]['present'] = 0;
                }

                if($v->att_status == 1){
                    $classes_and_values[$v->classes_name][$date]['present'] += 1;
                }

                if(empty($classes_and_values[$v->classes_name][$date]['total'])){
                    $classes_and_values[$v->classes_name][$date]['total'] = 1;
                }else{
                    $classes_and_values[$v->classes_name][$date]['total'] += 1;
                }

                $classes_and_values[$v->classes_name][$date]['average'] = number_format(($classes_and_values[$v->classes_name][$date]['present'] / $classes_and_values[$v->classes_name][$date]['total']) * 100, 1);

            }
        }


        $class_dates = array_unique($class_dates);
        $classes = array_unique($classes);

        return array($class_dates, $classes, $classes_and_values);
    }
}
