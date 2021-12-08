@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Attendance - {{$selected_class->classes_name}}</h2>
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
                @foreach($result as $student)
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
                    @if($student->att_status == 1)
                    <script>
                        $('.attendance-' + '{{$student->user_no}}').prop('checked', true);
                    </script>
                    @endif
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

        <button style="max-height: 50px; padding: 10px; margin-right: auto; margin-top: 40px;">Save</button>
    </form>
    
</div>
@endsection
