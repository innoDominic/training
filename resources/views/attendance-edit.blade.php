@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: column; justify-content: center;">
     <h2 style="margin-bottom:0px !important;">Attendance - {{$selected_class->classes_name}}</h2>
     <h4 style="margin-bottom:0px !important;">Period: {{$selected_period}}</h4>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <form style="display: table; width: 100%;" method="POST" action="/teacher/attendance/update">
        @method('PUT')
        <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Present</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @foreach($students as $student)
                    <tr>
                        <td>{{$student->student_id}}</td>
                        <td>{{$student->last_name}}, {{$student->first_name}}</td>
                        <td>
                            <input type="hidden" name="attendance-{{$i}}" class="attendance-{{$student->user_no}}" value="0" />
                            @csrf
                            
                            <input type="checkbox" name="attendance-{{$i}}" class="attendance-{{$student->user_no}}" value="1" />
                            @csrf

                            <input type="text" value="{{$student->plot_no}}" name="plotted_class_no_{{$i}}" hidden/>
                            @csrf
                        </td>
                    </tr>
                    @foreach($attendance as $att)
                        @if($student->plot_no == $att->plot_no)
                            @if($att->att_status == 1)
                                <script>
                                    $('.attendance-' + '{{$student->user_no}}').prop('checked', true);
                                </script>
                            @endif
                        @endif
                    @endforeach
                    @php
                        $i++;
                    @endphp
                @endforeach
            </tbody>
        </table>
        
        @php
            $selected_date = urldecode($selected_date);
        @endphp

        <input type="number" value="{{$i}}" name="student_count" hidden/>
        <input type="text" value="{{$selected_class->classes_no}}" name="selected_class" hidden/>
        <input type="text" value="{{$selected_date}}" name="attendance_date" hidden/>
        <input type="text" value="{{$selected_period}}" name="attendance_period" hidden/>

        <button style="max-height: 50px; padding: 10px; margin-right: auto; margin-top: 40px;">Save</button>
    </form>
    
</div>
@endsection
