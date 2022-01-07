@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
    <h2>Plot Students ( {{$selected_class_name}}, {{$selected_period}} )</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
@php #dd($students_unassigned, $students_assigned); @endphp
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students_assigned as $student)
                <tr>
                    <td>{{$student->student_id}}</td>
                    <td>{{$student->first_name}} {{$student->last_name}}</td>
                    <td>
                        <form action="{{ route('plot-class-teacher-student.destroy', ['student_plot_no' => $student->student_plot_no, 'teacher_plot_no' => $teacher_plot_no]) }}" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button style="color: white; background-color: red; cursor:pointer;">Remove</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center; margin-top:40px;">
    
    <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-teacher/plot-class-teacher-student">
        <div style="width:33%;">
            <label>
                Add Student:
                <select type="text" class="student_no" name="student_no" style="border: solid 1px black;">
                    @foreach($students_unassigned as $res)
                        <option value="{{$res->plot_no}}-{{$res->user_no}}">{{$res->student_id}}: {{$res->first_name}} {{$res->last_name}}</option>
                    @endforeach
                </select>
                @csrf
            </label>
            <input type="text" name="selected_class" class="selected_class" value="{{$selected_class}}" hidden/>
            <input type="text" name="selected_period" class="selected_period" value="{{$selected_period}}" hidden/>
            <input type="text" name="selected_class_name" class="selected_class_name" value="{{$selected_class_name}}" hidden/>
            <input type="text" name="teacher_plot_no" class="teacher_plot_no" value="{{$teacher_plot_no}}" hidden/>
            <input type="text" name="teacher_user_no" class="teacher_user_no" value="{{$selected_teacher}}" hidden/>
        </div>
        <div style="width: 33%; display: flex; flex-direction: column; justify-content: center;">
            <button style="max-width: 200px; margin: 0; margin-right: auto; max-height: 50px; padding: 10px;" class="addClassBtn">Add</button>
        </div>
        <div style="width: 33%; display: flex; flex-direction: column; justify-content: flex-start;">
            <h5 style="margin-top: 0px !important;">{{$result}}</h5>
        </div>
    </form>

</div>

@endsection
