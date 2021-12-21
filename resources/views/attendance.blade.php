@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Attendance</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Class</th>
                <th>Period</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $date = date('Y/m/d');
                $date = urlencode(urlencode($date));
            @endphp
            @foreach($result as $classes)
                @php
                    $period = urlencode(urlencode($classes->period));
                @endphp
                 <tr>
                     <td>{{$classes->classes_name}}</td>
                     <td>{{$classes->period}}</td>
                     <td><a href="/teacher/attendance/{{$classes->classes_no}}/{{$date}}/{{$period}}/edit">Take Attendance</a></td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    
</div>
@endsection
