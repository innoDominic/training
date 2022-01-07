@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
    <h2>Plot Teachers</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <form id="searchForm" style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-teacher">
        <div style="width:80%;">
            <label>
                Select Teacher:
                <select type="text" class="selected_teacher" name="selected_teacher" style="border: solid 1px black;">
                    @foreach($teacher_options as $teacher)
                        <option value="{{$teacher->user_no}}">({{$teacher->teacher_id}}) {{$teacher->teacher_title}} {{$teacher->first_name}} {{$teacher->last_name}} </option>
                    @endforeach
                </select>
                @csrf
            </label>
            <label>
                    Select Period:
                    <select type="text" class="selected_period" name="selected_period" style="border: solid 1px black;">
                        <option value="08:00 AM - 09:00 AM">08:00 AM - 09:00 AM</option>
                        <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                        <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                        <option value="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</option>
                        <option value="01:00 PM - 02:00 PM">01:00 PM - 02:00 PM</option>
                        <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                        <option value="03:00 PM - 04:00 PM">03:00 PM - 04:00 PM</option>
                        <option value="04:00 PM - 05:00 PM">04:00 PM - 05:00 PM</option>
                    </select>
                </label>
        </div>
        <div style="width: 20%; display: flex; flex-direction: column; justify-content: flex-end;">
            <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">View</button>
        </div>
    </form>

    <script>
        $(".selected_teacher").val("{{$selected_teacher}}");
        $(".selected_period").val("{{$selected_period}}");
    </script>

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
            @foreach($class_table_results as $class)
                <tr>
                    <td>{{$class->classes_name}}</td>
                    <td>{{$class->period}}</td>
                    <td style="display:flex; flex-direction: row; justify-content: space-evenly;">
                    <a href="/admin/plot-teacher/plot-periods/{{$class->plot_no}}/{{$class->classes_name}}/{{$class->classes_no}}/{{$class->period}}/{{$selected_teacher}}/edit">Link Students</a>    
                    | <form action="{{ route('plot_class_teacher.destroy', ['class_no' => $class->classes_no, 'selected_teacher' => $selected_teacher, 'period' => $class->period]) }}" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button style="color: white; background-color: red; cursor:pointer;">Delete</button>
                        </form>
                
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center; margin-top:40px;">
    
    <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-teacher/plot-class-teacher">
        <div style="width:33%;">
            <label>
                Add Class:
                <select type="text" class="class_no" name="class_no" style="border: solid 1px black;">
                    @foreach($class_options as $class)
                        <option value="{{$class->classes_no}}">{{$class->classes_name}}</option>
                    @endforeach
                </select>
                @csrf
            </label>
            <input type="text" class="selected_teacher_to_plot" name="selected_teacher_to_plot" value="{{$selected_teacher}}" hidden />

            <input type="text" class="selected_period_to_plot" name="selected_period_to_plot" value="{{$selected_period}}" hidden />   
            @csrf 
        </div>
        <div style="width: 33%; display: flex; flex-direction: column; justify-content: center;">
            <button style="max-width: 200px; margin: 0; margin-right: auto; max-height: 50px; padding: 10px;" class="addClassBtn">Add</button>
        </div>
        <div style="width: 33%; display: flex; flex-direction: column; justify-content: flex-start;">
            <h3 style="margin-top: 0px !important;">{{$result}}</h3>
        </div>
    </form>

</div>

@endsection
