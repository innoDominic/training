@extends('layouts.layout-with-nav')

@section('content')
<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Attendance Reports</h2>
     <button onclick="window.open('/teacher/attendance/report/csv')" style="max-height: 50px; padding: 10px;">CSV Download</button>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">

        <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
            <thead>
                <tr>
                    <th>Classes</th>
                    @foreach($dates as $date)
                        <th>{{$date}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($averages as $class => $dates)
                    <tr>
                        <td>{{$class}}</td>
                        @foreach($dates as $date => $value)
                           <td>
                               {{$value['average']}}
                           </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    
</div>
@endsection
