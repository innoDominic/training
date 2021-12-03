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
        
        <input type="number" value="{{$i}}" name="student_count" hidden/>
        <input type="text" value="{{$selected_class->classes_no}}" name="selected_class" hidden/>

        @php
            $date = date('Y-m-d',strtotime($selected_date))
        @endphp

        <input type="date" id="attendance_date_id" name="attendance_date" value="{{$date}}" />

        <button style="max-height: 50px; padding: 10px; margin-right: auto; margin-top: 40px;">Save</button>

        <script>
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            
            document.getElementById('attendace_date_id').setAttribute('min', today);
        </script>
    </form>
    
</div>
@endsection
