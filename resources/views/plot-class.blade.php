<?php use Illuminate\Support\Str; ?>

@extends('layouts.layout-with-nav')

@section('content')

<div style="width: 100%; display:flex; flex-direction: row; justify-content: flex-start; align-items: center;">
     <h2>Plot Classes</h2>
</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
     <form id="searchForm" style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-class">
         <div style="width:50%;">
             <label>
                 Select Class:
                 <select type="text" class="selected_class" name="selected_class" style="border: solid 1px black;">
                     @foreach($class_options as $class)
                         <option value="{{$class->classes_no}}">{{$class->classes_name}}</option>
                     @endforeach
                 </select>
                 @csrf
             </label>     
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;">View</button>
         </div>
     </form>

     <script>
         $(".selected_class").val("{{$selected_class}}");
     </script>

</div>
<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center;">
    
    <table class="table" style="border: solid 1px black; border-collapse: collapse; margin-top: 40px; width: 100%;">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             @foreach($student_table_results as $student)
                 <tr>
                     <td>{{$student->student_id}}</td>
                     <td>{{$student->last_name}} {{$student->first_name}}</td>
                     <td><a href="/admin/plot-class/delete?id={{$student->user_no}}&class={{$selected_class}}">Remove</a></td>
                 </tr>
             @endforeach
        </tbody>
    </table>
    
</div>

<div style="width: 100%; display:flex; flex-direction: column; justify-content: flex-start; align-items: center; margin-top:40px;">
    
     <form style="display: flex; flex-direction: row; flex-wrap: wrap; padding: 20px; width: 100%; border: solid 1px black;" method="POST" action="/admin/plot-class/plot-student">
         <div style="width:50%;">
             <label>
                 Add Student:
                 <select type="text" class="user_no" name="user_no" style="border: solid 1px black;">
                     @foreach($student_options as $student)
                         <option value="{{$student->user_no}}">({{$student->student_id}}) {{$student->last_name}}, {{$student->first_name}}</option>
                     @endforeach
                 </select>
                 @csrf
             </label>
             <input type="text" class="selected_class_to_plot" name="selected_class_to_plot" value="{{$selected_class}}" hidden />   
             @csrf 
         </div>
         <div style="width: 50%; display: flex; flex-direction: column; justify-content: flex-end;">
             <button style="max-width: 200px; margin: 0 auto; max-height: 50px; padding: 10px;" class="addClassBtn">Add</button>
         </div>
     </form>

</div>

@endsection
